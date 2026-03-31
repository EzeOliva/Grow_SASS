<?php

namespace App\Services;

use App\Models\WhatsappMessage;
use App\Models\WhatsappTicket;
use App\Models\WhatsappConnection;
use Illuminate\Support\Facades\Log;

/**
 * @fileoverview WhatsApp Message Service
 * @description Handles WhatsApp message processing, sending, and delivery status
 */
class WhatsappMessageService
{
    /**
     * Send a WhatsApp message
     */
    public function sendMessage(WhatsappMessage $message, WhatsappTicket $ticket): bool
    {
        try {
            $connection = WhatsappConnection::find($ticket->connection_id);
            
            if (!$connection || !$connection->is_active) {
                throw new \Exception('WhatsApp connection not available');
            }

            // Update message status to sending
            $message->update(['status' => 'sending']);

            // Send via appropriate provider based on connection type
            $success = $this->sendViaProvider($message, $connection);

            if ($success) {
                $message->update(['status' => 'sent']);
                return true;
            } else {
                $message->update(['status' => 'failed']);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp message sending failed: ' . $e->getMessage(), [
                'message_id' => $message->id,
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);

            $message->update(['status' => 'failed']);
            return false;
        }
    }

    /**
     * Send message via appropriate WhatsApp provider
     */
    private function sendViaProvider(WhatsappMessage $message, WhatsappConnection $connection): bool
    {
        switch ($connection->connection_type) {
            case 'baileys':
                return $this->sendViaBaileys($message, $connection);
            
            case 'twilio':
                return $this->sendViaTwilio($message, $connection);
            
            case '360dialog':
                return $this->sendVia360Dialog($message, $connection);
            
            case 'gupshup':
                return $this->sendViaGupshup($message, $connection);
            
            default:
                Log::warning('Unknown WhatsApp connection type: ' . $connection->connection_type);
                return false;
        }
    }

    /**
     * Send via Baileys provider
     */
    private function sendViaBaileys(WhatsappMessage $message, WhatsappConnection $connection): bool
    {
        try {
            // Implementation for Baileys
            // This would integrate with your Baileys setup
            Log::info('Sending via Baileys', ['message_id' => $message->id]);
            
            // Placeholder for actual Baileys integration
            return true;
            
        } catch (\Exception $e) {
            Log::error('Baileys sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via Twilio provider
     */
    private function sendViaTwilio(WhatsappMessage $message, WhatsappConnection $connection): bool
    {
        try {
            // Implementation for Twilio
            // This would integrate with your Twilio setup
            Log::info('Sending via Twilio', ['message_id' => $message->id]);
            
            // Placeholder for actual Twilio integration
            return true;
            
        } catch (\Exception $e) {
            Log::error('Twilio sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via 360Dialog provider
     */
    private function sendVia360Dialog(WhatsappMessage $message, WhatsappConnection $connection): bool
    {
        try {
            // Implementation for 360Dialog
            // This would integrate with your 360Dialog setup
            Log::info('Sending via 360Dialog', ['message_id' => $message->id]);
            
            // Placeholder for actual 360Dialog integration
            return true;
            
        } catch (\Exception $e) {
            Log::error('360Dialog sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via Gupshup provider
     */
    private function sendViaGupshup(WhatsappMessage $message, WhatsappConnection $connection): bool
    {
        try {
            // Implementation for Gupshup
            // This would integrate with your Gupshup setup
            Log::info('Sending via Gupshup', ['message_id' => $message->id]);
            
            // Placeholder for actual Gupshup integration
            return true;
            
        } catch (\Exception $e) {
            Log::error('Gupshup sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark message as delivered
     */
    public function markAsDelivered(string $messageId): bool
    {
        try {
            $message = WhatsappMessage::find($messageId);
            if ($message) {
                $message->update(['status' => 'delivered']);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark message as delivered: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark message as read
     */
    public function markAsRead(string $messageId): bool
    {
        try {
            $message = WhatsappMessage::find($messageId);
            if ($message) {
                $message->update([
                    'status' => 'read',
                    'read_at' => now()
                ]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark message as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process incoming WhatsApp message
     */
    public function processIncomingMessage(array $data): WhatsappMessage
    {
        try {
            // Find or create ticket
            $ticket = $this->findOrCreateTicketFromIncoming($data);
            
            // Create message record
            $message = WhatsappMessage::create([
                'tenant_id' => $ticket->tenant_id,
                'ticket_id' => $ticket->id,
                'sender_type' => 'client',
                'sender_id' => $data['sender_id'] ?? null,
                'sender_name' => $data['sender_name'] ?? 'Unknown Client',
                'channel' => 'whatsapp',
                'body' => $data['body'],
                'status' => 'received',
                'message_id' => $data['message_id'] ?? null,
                'attachments' => $data['attachments'] ?? [],
                'metadata' => $data['metadata'] ?? []
            ]);

            // Update ticket
            $ticket->update([
                'status' => 'open',
                'last_client_message_at' => now(),
                'updated_at' => now()
            ]);

            return $message;

        } catch (\Exception $e) {
            Log::error('Failed to process incoming WhatsApp message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Find or create ticket from incoming message
     */
    private function findOrCreateTicketFromIncoming(array $data): WhatsappTicket
    {
        $ticket = WhatsappTicket::where('connection_id', $data['connection_id'])
            ->where('contact_id', $data['contact_id'])
            ->where('tenant_id', $data['tenant_id'])
            ->first();

        if (!$ticket) {
            $ticket = WhatsappTicket::create([
                'tenant_id' => $data['tenant_id'],
                'connection_id' => $data['connection_id'],
                'contact_id' => $data['contact_id'],
                'status' => 'open',
                'priority' => 'medium',
                'channel' => 'whatsapp',
                'subject' => 'New WhatsApp conversation',
                'assigned_to' => null
            ]);
        }

        return $ticket;
    }
}
