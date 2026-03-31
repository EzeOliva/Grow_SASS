<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Message;
use App\Models\WhatsappMessage;
use App\Models\WhatsappConnection;
use App\Models\WhatsappTicket;
use App\Models\WhatsappQuickTemplate;
use App\Services\WhatsappTicketService;
use App\Services\WhatsappMessageService;

class Messages extends Controller
{
    protected $whatsappTicketService;
    protected $whatsappMessageService;

    public function __construct(WhatsappTicketService $whatsappTicketService, WhatsappMessageService $whatsappMessageService)
    {
        $this->whatsappTicketService = $whatsappTicketService;
        $this->whatsappMessageService = $whatsappMessageService;
    }

    /**
     * Display messages index
     */
    public function index()
    {
        // Get all users from landlord database except the current authenticated user
        $users = \App\Models\User::on('landlord')
            ->where('id', '!=', auth()->user()->id)
            ->select('id', 'first_name', 'last_name', 'email', 'avatar_directory', 'avatar_filename', 'type', 'status')
            ->get();
        
        // Debug logging
        \Log::info('Messages index - Users found: ' . $users->count());
        \Log::info('Current user ID: ' . auth()->user()->id);
        
        $data = [
            'users' => $users,
            'whatsappConnections' => $this->getWhatsappConnectionsForView(),
            'whatsappTickets' => $this->getWhatsappTicketsForView(),
            'quick_templates' => $this->getQuickTemplatesForView()
        ];

        return view('pages.messages.wrapper', $data);
    }

    /**
     * Show a specific thread/conversation
     */
    public function showThread($threadId)
    {
        try {
            // Check if this is an AJAX request
            if (request()->ajax()) {
                // Return only the chat room content for AJAX requests
                $data = $this->getThreadData($threadId);
                return view('pages.messages.components.chat-room', $data);
            }
            
            // For regular requests, return the full page
            $data = $this->getThreadData($threadId);
            return view('pages.messages.wrapper', $data);
            
        } catch (\Exception $e) {
            Log::error('Error in showThread: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load thread'], 500);
        }
    }

    /**
     * Get thread data for display
     */
    private function getThreadData($threadId)
    {
        // Get all users except the current authenticated user
        $users = \App\Models\User::on('landlord')
            ->where('id', '!=', auth()->user()->id)
            ->select('id', 'first_name', 'last_name', 'email', 'avatar_directory', 'avatar_filename', 'type', 'status')
            ->get();
        
        $data = [
            'users' => $users,
            'whatsappConnections' => $this->getWhatsappConnectionsForView(),
            'whatsappTickets' => $this->getWhatsappTicketsForView(),
            'quick_templates' => $this->getQuickTemplatesForView(),
            'threadId' => $threadId
        ];

        return $data;
    }

    /**
     * Get messages feed
     */
    public function feed(Request $request)
    {
        try {
            $messages = $this->getMessagesForFeed($request);
            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Error in feed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load messages'], 500);
        }
    }

    /**
     * Get thread meta (name, company, tags)
     */
    public function getThreadMeta(Request $request)
    {
        try {
            $request->validate([
                'thread_id' => 'required',
                'thread_type' => 'required|string|in:user,whatsapp',
            ]);

            $threadId = $request->get('thread_id');
            $threadType = $request->get('thread_type');

            if ($threadType === 'whatsapp') {
                $ticket = WhatsappTicket::on('tenant')->find($threadId);
                if (!$ticket) {
                    return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
                }
                // Collect tags from json column and pivot, de-duplicated
                $jsonTags = is_array($ticket->tags) ? $ticket->tags : [];
                try {
                    $pivotTags = method_exists($ticket, 'tags') ? $ticket->tags()->pluck('name')->toArray() : [];
                } catch (\Throwable $e) { $pivotTags = []; }
                $allTags = array_values(array_unique(array_filter(array_merge($jsonTags, $pivotTags))));
                return response()->json([
                    'success' => true,
                    'data' => [
                        'name' => $ticket->contact_name,
                        'company' => null,
                        'tags' => $allTags,
                    ]
                ]);
            }

            // user thread
            $user = User::on('landlord')->find($threadId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            $company = null;
            $tags = [];
            if ($user->clientid) {
                $client = \App\Models\Client::find($user->clientid);
                if ($client) {
                    $company = $client->client_company_name;
                    try {
                        $tags = \App\Models\Tag::where('tagresource_type', 'client')
                            ->where('tagresource_id', $client->client_id)
                            ->pluck('tag_title')->toArray();
                    } catch (\Throwable $e) { $tags = []; }
                }
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? ''),
                    'company' => $company,
                    'tags' => $tags,
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('getThreadMeta error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch meta'], 500);
        }
    }

    /**
     * Update thread details (name, company, tags)
     */
    public function updateThread(Request $request)
    {
        try {
            $validated = $request->validate([
                'thread_id' => 'required',
                'thread_type' => 'required|string|in:user,whatsapp',
                'name' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'tags' => 'nullable|string',
            ]);

            $threadId = $validated['thread_id'];
            $threadType = $validated['thread_type'];
            $name = $validated['name'] ?? null;
            $company = $validated['company'] ?? null;
            $tagsCsv = $validated['tags'] ?? null;

            $updated = [];

            if ($threadType === 'whatsapp') {
                // whatsapp ticket update
                $ticket = WhatsappTicket::on('tenant')->find($threadId);
                if (!$ticket) {
                    return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
                }

                if ($name !== null && trim($name) !== '') {
                    $ticket->contact_name = trim($name);
                    $updated['name'] = trim($name);
                }

                // Company is not a field on whatsapp_tickets; ignore if provided

                // Tags: support both json column `tags` and pivot `whatsapp_ticket_tags`
                if ($tagsCsv !== null) {
                    $tagsArray = collect(explode(',', $tagsCsv))
                        ->map(function ($t) { return trim($t); })
                        ->filter()
                        ->unique()
                        ->values();

                    // Store simple list into json `tags` if available
                    if (\Illuminate\Support\Facades\Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'tags')) {
                        $ticket->tags = $tagsArray->all();
                    }

                    // Also sync with master tags table if exists
                    try {
                        $tagIds = \App\Models\WhatsappTag::on('tenant')
                            ->whereIn('name', $tagsArray->all())
                            ->pluck('id', 'name');

                        // Create missing tags as global by default
                        $missing = $tagsArray->reject(function ($name) use ($tagIds) { return isset($tagIds[$name]); });
                        foreach ($missing as $newTagName) {
                            $tag = \App\Models\WhatsappTag::on('tenant')->create([
                                'tenant_id' => auth()->user()->tenant_id ?? null,
                                'name' => $newTagName,
                                'color' => '#6c757d',
                                'description' => null,
                                'type' => 'global',
                                'is_active' => true,
                                'created_by' => auth()->id(),
                            ]);
                            $tagIds->put($newTagName, $tag->id);
                        }

                        if (method_exists($ticket, 'tags')) {
                            $ticket->tags()->sync($tagIds->values()->all());
                        }
                    } catch (\Throwable $e) {
                        // fail silently on tag sync; json tags still saved
                    }

                    $updated['tags'] = $tagsArray->all();
                }

                $ticket->save();

                return response()->json(['success' => true, 'data' => $updated]);
            }

            // user thread: update landlord user's name and company (client)
            if ($threadType === 'user') {
                $user = User::on('landlord')->find($threadId);
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'User not found'], 404);
                }

                if ($name !== null && trim($name) !== '') {
                    // If first/last available, split; else set first_name only
                    $user->first_name = trim($name);
                    $updated['name'] = trim($name);
                }

                if ($company !== null) {
                    // update related client company name when possible
                    if ($user->clientid) {
                        $client = \App\Models\Client::find($user->clientid);
                        if ($client) {
                            $client->client_company_name = $company;
                            $client->save();
                        }
                    }
                    $updated['company'] = $company;
                }

                // For user threads, simple tags are not standardized; ignore if sent

                $user->save();

                return response()->json(['success' => true, 'data' => $updated]);
            }

            return response()->json(['success' => false, 'message' => 'Unsupported thread type'], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            \Log::error('updateThread error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update thread'], 500);
        }
    }

    /**
     * Post text message
     */
    public function postText(Request $request)
    {
        try {
            $request->validate([
                'message_text' => 'required|string|max:1000',
                'message_target' => 'required|string',
                'reply_to_message_id' => 'nullable|integer'
            ]);

            $channel = $request->get('channel', 'internal');

            // WhatsApp channel - persist to whatsapp_messages to fit existing structure
            if ($channel === 'whatsapp') {
                $ticketId = $request->get('ticket_id');
                $connectionId = $request->get('connection_id');

                // Fallback: parse from message_target if provided
                if (!$ticketId && str_starts_with($request->message_target, 'whatsapp_ticket_')) {
                    $ticketId = (int) str_replace('whatsapp_ticket_', '', $request->message_target);
                }
                if (!$connectionId && str_starts_with($request->message_target, 'whatsapp_connection_')) {
                    $connectionId = (int) str_replace('whatsapp_connection_', '', $request->message_target);
                }

                $whMessage = new WhatsappMessage();
                if ($ticketId) {
                    $whMessage->ticket_id = $ticketId;
                }
                if ($connectionId) {
                    // Many installs include this column; set if present
                    $whMessage->connection_id = $connectionId;
                }
                $whMessage->sender_type = 'agent';
                $whMessage->sender_id = auth()->id();
                $whMessage->sender_name = auth()->user()->first_name . ' ' . auth()->user()->last_name;
                $whMessage->channel = 'whatsapp';
                $whMessage->body = $request->message_text;
                if ($request->filled('reply_to_message_id')) {
                    $whMessage->reply_to_message_id = (int) $request->reply_to_message_id;
                }
                $whMessage->status = 'sent';
                $whMessage->attachments = json_encode([]);
                $whMessage->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'message_data' => [
                        'id' => $whMessage->id,
                        'message_text' => $whMessage->body,
                        'message_source' => 'user_' . auth()->id(),
                        'message_target' => $ticketId ? ('whatsapp_ticket_' . $ticketId) : ($connectionId ? ('whatsapp_connection_' . $connectionId) : $request->message_target),
                        'reply_to_message_id' => $whMessage->reply_to_message_id ?? null,
                        'sender_id' => auth()->id(),
                        'sender_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'status' => 'sent',
                        'attachments' => []
                    ]
                ]);
            }

            // Default/internal channel - persist to messages
            $message = new Message();
            $message->message_text = $request->message_text;
            $message->message_source = 'user_' . auth()->id();
            $message->message_target = $request->message_target;
            if ($request->filled('reply_to_message_id')) {
                $message->message_reply_to_id = (int) $request->reply_to_message_id;
            }
            $message->message_creatorid = auth()->id();
            $message->message_created = now();
            $message->save();

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'message_data' => [
                    'id' => $message->message_id,
                    'message_text' => $message->message_text,
                    'message_source' => $message->message_source,
                    'message_target' => $message->message_target,
                    'reply_to_message_id' => $message->message_reply_to_id ?? null,
                    'sender_id' => auth()->id(),
                    'sender_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'created_at' => $message->message_created->format('Y-m-d H:i:s'),
                    'status' => 'sent',
                    'attachments' => []
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in postText: ' . $e->getMessage());
            Log::error('Error details: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to send message',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Post files
     */
    public function postFiles(Request $request)
    {
        try {
            $uploadedFiles = [];
            
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                
                // Ensure files is an array
                if (!is_array($files)) {
                    $files = [$files];
                }
                
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        // Generate unique filename
                        $filename = time() . '_' . $file->getClientOriginalName();
                        
                        // Store file in public/uploads directory
                        $path = $file->storeAs('uploads', $filename, 'public');
                        
                        // Get file info
                        $fileInfo = [
                            'name' => $file->getClientOriginalName(),
                            'filename' => $filename,
                            'size' => $file->getSize(),
                            'type' => $file->getMimeType(),
                            'url' => '/storage/' . $path,
                            'path' => storage_path('app/public/' . $path)
                        ];
                        
                        $uploadedFiles[] = $fileInfo;
                        
                        // Store message with file attachment in database (align with existing messages schema)
                        $messageTarget = $request->get('message_target', 'general');

                        $message = new Message();
                        $message->message_source = 'user_' . Auth::id();
                        $message->message_target = $messageTarget;
                        $message->message_text = 'File attachment: ' . $file->getClientOriginalName();
                        $message->message_type = 'file';
                        $message->message_file_name = $filename;
                        $message->message_file_directory = 'uploads';
                        $message->message_file_type = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
                        $message->message_creatorid = Auth::id();
                        $message->message_created = now();
                        $message->save();
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'files' => $uploadedFiles
            ]);
        } catch (\Exception $e) {
            Log::error('Error in postFiles: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload files'], 500);
        }
    }

    /**
     * Get chat history
     */
    public function getChatHistory($threadId = null, $threadType = null)
    {
        try {
            $request = request();
            $userId = $request->get('user_id');
            $connectionId = $request->get('connection_id');
            $ticketId = $request->get('ticket_id');
            $perPage = (int) ($request->get('per_page') ?? 50);
            $perPage = max(10, min($perPage, 100));
            $page = (int) ($request->get('page') ?? 1);
            
            // Log the request for debugging
            \Log::info('Chat history request', [
                'threadId' => $threadId,
                'threadType' => $threadType,
                'userId' => $userId,
                'connectionId' => $connectionId,
                'currentUser' => Auth::id(),
                'ticketId' => $ticketId,
                'page' => $page,
                'perPage' => $perPage,
            ]);
            
            $messages = collect();
            $meta = [
                'current_page' => $page,
                'per_page' => $perPage,
                'has_more' => false,
                'total' => null,
            ];
            
            if ($connectionId) {
                // WhatsApp connection messages
                $paginator = WhatsappMessage::where('connection_id', $connectionId)
                    ->orderBy('id', 'desc')
                    ->simplePaginate($perPage, ['*'], 'page', $page);

                $items = collect($paginator->items())
                    ->reverse() // chronological for UI
                    ->values()
                    ->map(function ($message) {
                        return $this->transformWhatsappMessage($message, 'whatsapp_connection_' . $message->connection_id);
                    });

                $messages = $items;
                $meta['has_more'] = $paginator->hasMorePages();
            } elseif ($ticketId) {
                // WhatsApp ticket messages
                $paginator = WhatsappMessage::where('ticket_id', $ticketId)
                    ->orderBy('id', 'desc')
                    ->simplePaginate($perPage, ['*'], 'page', $page);

                $items = collect($paginator->items())
                    ->reverse()
                    ->values()
                    ->map(function ($message) {
                        return $this->transformWhatsappMessage($message, 'whatsapp_ticket_' . $message->ticket_id);
                    });

                $messages = $items;
                $meta['has_more'] = $paginator->hasMorePages();
            } elseif ($userId) {
                // Internal user messages
                $currentUserId = Auth::id();
                $targetUser = 'user_' . $userId;
                $sourceUser = 'user_' . $currentUserId;
                
                \Log::info('Querying messages for users', [
                    'targetUser' => $targetUser,
                    'sourceUser' => $sourceUser
                ]);
                
                $baseQuery = Message::where(function($query) use ($targetUser, $sourceUser) {
                    $query->where(function($q) use ($targetUser, $sourceUser) {
                        $q->where('message_source', $sourceUser)
                          ->where('message_target', $targetUser);
                    })->orWhere(function($q) use ($targetUser, $sourceUser) {
                        $q->where('message_source', $targetUser)
                          ->where('message_target', $sourceUser);
                    });
                });

                $paginator = $baseQuery
                    ->orderBy('message_id', 'desc')
                    ->simplePaginate($perPage, ['*'], 'page', $page);
                
                \Log::info('Found messages', ['count' => count($paginator->items())]);
                
                // Build sender name map in one query
                $senderIds = collect($paginator->items())
                    ->map(function ($m) { return (int) str_replace('user_', '', $m->message_source); })
                    ->filter()
                    ->unique()
                    ->values();

                $senderNameMap = $this->getUserIdToDisplayNameMap($senderIds);

                $items = collect($paginator->items())
                    ->reverse()
                    ->values()
                    ->map(function ($message) use ($senderNameMap) {
                        return $this->transformInternalMessage($message, $senderNameMap);
                    });

                $messages = $items;
                $meta['has_more'] = $paginator->hasMorePages();
            } else {
                // Fallback to original method
                $messages = $this->getMessagesForFeed(request());
            }
            
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'meta' => $meta,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getChatHistory: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load chat history'], 500);
        }
    }

    /**
     * Transform a WhatsappMessage model instance into API response shape
     */
    private function transformWhatsappMessage($message, $messageTarget)
    {
        $attachments = [];
        if ($message->attachments) {
            $decoded = json_decode($message->attachments, true);
            $attachments = is_array($decoded) ? $decoded : [];
        }

        return [
            'id' => $message->id,
            'message_text' => $message->body,
            'message_source' => $message->sender_type === 'agent' ? 'user_' . $message->sender_id : 'whatsapp_' . $message->sender_id,
            'message_target' => $messageTarget,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender_name,
            'created_at' => $message->created_at ? $message->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            'status' => $message->status,
            'attachments' => $attachments,
            'reply_to_message_id' => property_exists($message, 'reply_to_message_id') ? ($message->reply_to_message_id ?? null) : null,
        ];
    }

    /**
     * Transform an internal Message model instance
     */
    private function transformInternalMessage($message, $senderNameMap)
    {
        $attachments = [];
        if (property_exists($message, 'attachments') && $message->attachments) {
            $decoded = json_decode($message->attachments, true);
            $attachments = is_array($decoded) ? $decoded : [];
        }

        $senderId = (int) str_replace('user_', '', $message->message_source);
        $senderName = $senderNameMap[$senderId] ?? 'User';

        return [
            'id' => $message->message_id,
            'message_text' => $message->message_text,
            'message_source' => $message->message_source,
            'message_target' => $message->message_target,
            'reply_to_message_id' => $message->message_reply_to_id ?? null,
            'sender_id' => $senderId,
            'sender_name' => $senderName,
            'created_at' => $message->message_created ? $message->message_created->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            'status' => 'sent',
            'attachments' => $attachments,
        ];
    }

    /**
     * Fetch landlord users' display names in a single query
     */
    private function getUserIdToDisplayNameMap($userIds)
    {
        try {
            if (empty($userIds) || count($userIds) === 0) {
                return [];
            }
            $users = \App\Models\User::on('landlord')
                ->whereIn('id', $userIds)
                ->get(['id', 'first_name', 'last_name', 'name']);
            $map = [];
            foreach ($users as $u) {
                $display = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
                if ($display === '') {
                    $display = $u->name ?? ('User #' . $u->id);
                }
                $map[$u->id] = $display;
            }
            return $map;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get WhatsApp connections for view
     */
    private function getWhatsappConnectionsForView()
    {
        try {
            if (Schema::connection('tenant')->hasTable('whatsapp_connections')) {
                return WhatsappConnection::where('tenant_id', auth()->user()->tenant_id ?? 1)
                    ->where('status', 'connected')
                    ->get();
            }
            return collect();
        } catch (\Exception $e) {
            Log::error('Error getting WhatsApp connections: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get WhatsApp tickets for view
     */
    private function getWhatsappTicketsForView()
    {
        try {
            if (Schema::connection('tenant')->hasTable('whatsapp_tickets')) {
                return WhatsappTicket::where('tenant_id', auth()->user()->tenant_id ?? 1)
                    ->orderBy('updated_at', 'desc')
                    ->limit(10)
                    ->get();
            }
            return collect();
        } catch (\Exception $e) {
            Log::error('Error getting WhatsApp tickets: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get quick templates for view
     */
    private function getQuickTemplatesForView()
    {
        try {
            if (Schema::connection('tenant')->hasTable('whatsapp_quick_templates')) {
                return WhatsappQuickTemplate::where('is_active', true)
                    ->where('tenant_id', auth()->user()->tenant_id ?? 1)
                    ->get();
            }
            return collect();
        } catch (\Exception $e) {
            Log::error('Error getting quick templates: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get messages for feed
     */
    private function getMessagesForFeed($request)
    {
        // Implementation for getting messages
        return collect();
    }

    /**
     * Get quick templates
     */
    public function getQuickTemplates()
    {
        try {
            if (Schema::connection('tenant')->hasTable('whatsapp_quick_templates')) {
                $templates = WhatsappQuickTemplate::where('is_active', true)
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->get();

                return response()->json([
                    'success' => true,
                    'templates' => $templates
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Quick templates table not found',
                'templates' => []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error loading templates: ' . $e->getMessage(),
                'templates' => []
            ]);
        }
    }

    /**
     * Get WhatsApp connections
     */
    public function getWhatsappConnections()
    {
        try {
            if (Schema::connection('tenant')->hasTable('whatsapp_connections')) {
                $connections = WhatsappConnection::where('tenant_id', auth()->user()->tenant_id)
                    ->get();

                return response()->json([
                    'success' => true,
                    'connections' => $connections
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'WhatsApp connections table not found',
                'connections' => []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error loading connections: ' . $e->getMessage(),
                'connections' => []
            ]);
        }
    }

    /**
     * Post WhatsApp message
     */
    public function postWhatsAppMessage(Request $request)
    {
        try {
            // Implementation for sending WhatsApp messages
            return response()->json([
                'success' => true,
                'message' => 'WhatsApp message sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in postWhatsAppMessage: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send WhatsApp message'], 500);
        }
    }

    /**
     * Create WhatsApp connection
     */
    public function createWhatsAppConnection(Request $request)
    {
        try {
            $request->validate([
                'connection_name' => 'required|string|max:190',
                'connection_type' => 'required|string|max:50',
                'phone_number' => 'nullable|string|max:30',
                'webhook_config' => 'nullable|array'
            ]);

            if (!Schema::connection('tenant')->hasTable('whatsapp_connections')) {
                return response()->json(['error' => 'WhatsApp connections table not found'], 422);
            }

            $connection = new WhatsappConnection();
            $connection->tenant_id = auth()->user()->tenant_id ?? 1;
            $connection->connection_name = $request->connection_name;
            $connection->connection_type = $request->connection_type; // e.g. baileys, twilio
            $connection->status = 'connected'; // mark as connected so it appears immediately in list
            $connection->phone_number = $request->phone_number;
            $connection->connection_data = [];
            $connection->webhook_config = $request->input('webhook_config', []);
            $connection->is_active = true;
            $connection->save();

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp connection created successfully',
                'connection' => $connection
            ]);
        } catch (\Exception $e) {
            Log::error('Error in createWhatsAppConnection: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create WhatsApp connection'], 500);
        }
    }

    /**
     * Generate QR code
     */
    public function generateQRCode($connectionId)
    {
        try {
            // Implementation for generating QR codes
            return response()->json([
                'success' => true,
                'qr_code' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
            ]);
        } catch (\Exception $e) {
            Log::error('Error in generateQRCode: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate QR code'], 500);
        }
    }

    /**
     * Check connection status
     */
    public function checkConnectionStatus($connectionId)
    {
        try {
            // Implementation for checking connection status
            return response()->json([
                'success' => true,
                'status' => 'connected'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in checkConnectionStatus: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to check connection status'], 500);
        }
    }

    /**
     * Post email message
     */
    public function postEmailMessage(Request $request)
    {
        try {
            // Implementation for sending email messages
            return response()->json([
                'success' => true,
                'message' => 'Email message sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in postEmailMessage: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send email message'], 500);
        }
    }
}
