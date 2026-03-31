<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappMessage;
use App\Models\WhatsappTicket;
use App\Models\WhatsappQuickTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * @fileoverview Enhanced WhatsApp Message Controller
 * @description Handles advanced message features including emojis, quick templates, 
 * rich media, and enhanced composition capabilities
 */
class WhatsappMessageController extends Controller
{
    /**
     * Send a message with enhanced features
     */
    public function sendMessage(Request $request, WhatsappTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'channel' => 'required|in:whatsapp,email',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max
            'template_id' => 'nullable|exists:whatsapp_quick_templates,id',
            'reply_to_message_id' => 'nullable|exists:whatsapp_messages,id'
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            // Create the message
            $message = WhatsappMessage::create([
                'tenant_id' => $ticket->tenant_id,
                'ticket_id' => $ticket->id,
                'sender_type' => 'agent',
                'sender_id' => $user->id,
                'sender_name' => $user->first_name . ' ' . $user->last_name,
                'channel' => $request->channel,
                'body' => $this->processMessageContent($request->message),
                'status' => 'sending',
                'reply_to_message_id' => $request->reply_to_message_id,
                'attachments' => [],
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                $this->processAttachments($message, $request->file('attachments'));
            }

            // Update ticket status and activity
            $this->updateTicketActivity($ticket);

            // Send via actual channel
            $this->sendViaChannel($message, $ticket);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'message_id' => $message->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error sending message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process message content (emojis, formatting)
     */
    private function processMessageContent(string $content): string
    {
        // Convert emoji shortcuts to actual emojis
        $content = $this->convertEmojiShortcuts($content);
        
        // Process markdown-like formatting
        $content = $this->processFormatting($content);
        
        return $content;
    }

    /**
     * Convert emoji shortcuts to actual emojis
     */
    private function convertEmojiShortcuts(string $content): string
    {
        $shortcuts = [
            ':)' => '😊',
            ':(' => '😢',
            ';)' => '😉',
            ':D' => '😃',
            ':P' => '😛',
            ':o' => '😮',
            ':|' => '😐',
            ':*' => '😘',
            '<3' => '❤️',
            'thumbsup' => '👍',
            'thumbsdown' => '👎',
            'check' => '✅',
            'x' => '❌',
            'warning' => '⚠️',
            'info' => 'ℹ️',
            'clock' => '⏰',
            'phone' => '📞',
            'email' => '📧',
            'location' => '📍',
            'star' => '⭐'
        ];

        return str_replace(array_keys($shortcuts), array_values($shortcuts), $content);
    }

    /**
     * Process basic formatting
     */
    private function processFormatting(string $content): string
    {
        // Bold: **text** -> <strong>text</strong>
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        
        // Italic: *text* -> <em>text</em>
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
        
        // Code: `text` -> <code>text</code>
        $content = preg_replace('/`(.*?)`/', '<code>$1</code>', $content);
        
        // Line breaks
        $content = str_replace("\n", '<br>', $content);
        
        return $content;
    }

    /**
     * Count emojis in message
     */
    private function countEmojis(string $content): int
    {
        $emojiPattern = '/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u';
        preg_match_all($emojiPattern, $content, $matches);
        return count($matches[0]);
    }

    /**
     * Process message attachments
     */
    private function processAttachments(WhatsappMessage $message, array $attachments): void
    {
        $processedAttachments = [];
        
        foreach ($attachments as $attachment) {
            $filename = time() . '_' . $attachment->getClientOriginalName();
            $path = $attachment->storeAs('whatsapp/attachments', $filename, 'public');
            
            $processedAttachments[] = [
                'original_name' => $attachment->getClientOriginalName(),
                'filename' => $filename,
                'path' => $path,
                'size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
                'extension' => $attachment->getClientOriginalExtension()
            ];
        }
        
        $message->update([
            'attachments' => $processedAttachments
        ]);
    }

    /**
     * Get quick templates
     */
    public function getQuickTemplates(Request $request)
    {
        $templates = WhatsappQuickTemplate::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where('is_active', true)
            ->where('category', $request->get('category', 'general'))
            ->orderBy('sort_order')
            ->get();

        return response()->json($templates);
    }

    /**
     * Create quick template
     */
    public function createTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:4000',
            'category' => 'required|string|max:100',
            'shortcut' => 'nullable|string|max:50'
        ]);

        $template = WhatsappQuickTemplate::create([
            'tenant_id' => app('currentTenant')->id ?? 1, // Get from current tenant context
            'name' => $request->name,
            'content' => $request->content,
            'category' => $request->category,
            'shortcut' => $request->shortcut,
            'is_active' => true,
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Update ticket activity
     */
    private function updateTicketActivity(WhatsappTicket $ticket): void
    {
        $payload = [
            'status' => $ticket->status === 'closed' ? 'in_progress' : $ticket->status
        ];
        if (\Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'last_activity_at')) {
            $payload['last_activity_at'] = now();
        }
        $ticket->update($payload);

        // Set first response time if this is the first agent response
        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }
    }

    /**
     * Send message via actual channel
     */
    private function sendViaChannel(WhatsappMessage $message, WhatsappTicket $ticket): void
    {
        try {
            if ($message->channel === 'whatsapp') {
                // TODO: Implement actual WhatsApp sending
                $this->sendWhatsAppMessage($message, $ticket);
            } elseif ($message->channel === 'email') {
                // TODO: Implement actual email sending
                $this->sendEmailMessage($message, $ticket);
            }

            $message->update(['status' => 'sent']);
            
        } catch (\Exception $e) {
            $message->update(['status' => 'failed']);
            \Log::error("Failed to send message {$message->id}: " . $e->getMessage());
        }
    }

    /**
     * Send WhatsApp message (placeholder)
     */
    private function sendWhatsAppMessage(WhatsappMessage $message, WhatsappTicket $ticket): void
    {
        // TODO: Implement actual WhatsApp API integration
        \Log::info("WhatsApp message sent: {$message->body}");
    }

    /**
     * Send email message (placeholder)
     */
    private function sendEmailMessage(WhatsappMessage $message, WhatsappTicket $ticket): void
    {
        // TODO: Implement actual email sending
        \Log::info("Email message sent: {$message->body}");
    }

    /**
     * Get message suggestions based on context
     */
    public function getSuggestions(Request $request)
    {
        $context = $request->get('context', '');
        $category = $request->get('category', 'general');
        
        $suggestions = WhatsappQuickTemplate::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where('is_active', true)
            ->where(function($query) use ($context, $category) {
                $query->where('category', $category)
                      ->orWhere('content', 'like', "%{$context}%");
            })
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->get();

        return response()->json($suggestions);
    }
}
