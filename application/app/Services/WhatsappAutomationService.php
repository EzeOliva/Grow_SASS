<?php

namespace App\Services;

use App\Models\WhatsappTicket;
use App\Models\WhatsappMessage;
use App\Models\WhatsappLineConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @fileoverview WhatsApp Automation Service
 * @description Handles automated WhatsApp ticket operations including welcome messages, 
 * closure messages, inactivity handling, and auto-assignment
 */
class WhatsappAutomationService
{
    /**
     * Send welcome message when ticket is accepted
     */
    public function sendWelcomeMessage(WhatsappTicket $ticket): bool
    {
        try {
            if (!$ticket->lineConfig || !$ticket->lineConfig->welcome_message) {
                return false;
            }

            $message = WhatsappMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'system',
                'sender_name' => 'System',
                'channel' => $ticket->channel,
                'body' => $ticket->lineConfig->welcome_message,
                'status' => 'sent',
                'metadata' => ['automation_type' => 'welcome_message']
            ]);

            // Update ticket last activity
            $ticket->update(['last_activity_at' => now()]);

            Log::info("Welcome message sent for ticket {$ticket->id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome message for ticket {$ticket->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send closure message when ticket is closed
     */
    public function sendClosureMessage(WhatsappTicket $ticket): bool
    {
        try {
            if (!$ticket->lineConfig || !$ticket->lineConfig->closure_message) {
                return false;
            }

            $message = WhatsappMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'system',
                'sender_name' => 'System',
                'channel' => $ticket->channel,
                'body' => $ticket->lineConfig->closure_message,
                'status' => 'sent',
                'metadata' => ['automation_type' => 'closure_message']
            ]);

            Log::info("Closure message sent for ticket {$ticket->id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send closure message for ticket {$ticket->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send inactivity message before auto-closing
     */
    public function sendInactivityMessage(WhatsappTicket $ticket): bool
    {
        try {
            if (!$ticket->lineConfig || !$ticket->lineConfig->inactivity_message) {
                return false;
            }

            $message = WhatsappMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'system',
                'sender_name' => 'System',
                'channel' => $ticket->channel,
                'body' => $ticket->lineConfig->inactivity_message,
                'status' => 'sent',
                'metadata' => ['automation_type' => 'inactivity_message']
            ]);

            Log::info("Inactivity message sent for ticket {$ticket->id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send inactivity message for ticket {$ticket->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Auto-assign ticket based on line configuration
     */
    public function autoAssignTicket(WhatsappTicket $ticket): bool
    {
        try {
            if (!$ticket->lineConfig || !$ticket->lineConfig->isAutoAssignmentEnabled()) {
                return false;
            }

            $nextAgent = $ticket->lineConfig->getNextAgent();
            if (!$nextAgent) {
                Log::warning("No agent available for auto-assignment on ticket {$ticket->id}");
                return false;
            }

            $ticket->update([
                'agent_id' => $nextAgent->id,
                'status' => 'in_progress'
            ]);

            Log::info("Ticket {$ticket->id} auto-assigned to agent {$nextAgent->id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to auto-assign ticket {$ticket->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check and handle inactive tickets
     */
    public function checkInactiveTickets(): void
    {
        try {
            $inactiveTickets = WhatsappTicket::whereIn('status', ['open', 'in_progress'])
                ->whereNotNull('line_config_id')
                ->whereNotNull('last_activity_at')
                ->get();

            foreach ($inactiveTickets as $ticket) {
                if (!$ticket->lineConfig || !$ticket->lineConfig->hasInactivityTimeout()) {
                    continue;
                }

                $inactivityThreshold = $ticket->last_activity_at->addMinutes(
                    $ticket->lineConfig->inactivity_timeout_minutes
                );

                if (now()->gte($inactivityThreshold)) {
                    $this->handleInactiveTicket($ticket);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error checking inactive tickets: " . $e->getMessage());
        }
    }

    /**
     * Handle a single inactive ticket
     */
    private function handleInactiveTicket(WhatsappTicket $ticket): void
    {
        try {
            // Send inactivity message
            $this->sendInactivityMessage($ticket);

            // Schedule auto-close in 1 hour
            $ticket->update([
                'auto_close_scheduled_at' => now()->addHour(),
                'status' => 'on_hold'
            ]);

            Log::info("Ticket {$ticket->id} marked as inactive and scheduled for auto-close");
        } catch (\Exception $e) {
            Log::error("Failed to handle inactive ticket {$ticket->id}: " . $e->getMessage());
        }
    }

    /**
     * Process auto-close scheduled tickets
     */
    public function processAutoCloseScheduledTickets(): void
    {
        try {
            $scheduledTickets = WhatsappTicket::where('status', 'on_hold')
                ->whereNotNull('auto_close_scheduled_at')
                ->where('auto_close_scheduled_at', '<=', now())
                ->get();

            foreach ($scheduledTickets as $ticket) {
                $this->autoCloseTicket($ticket);
            }
        } catch (\Exception $e) {
            Log::error("Error processing auto-close scheduled tickets: " . $e->getMessage());
        }
    }

    /**
     * Auto-close a ticket
     */
    private function autoCloseTicket(WhatsappTicket $ticket): void
    {
        try {
            $ticket->update([
                'status' => 'closed',
                'closed_at' => now(),
                'auto_close_scheduled_at' => null
            ]);

            // Send closure message if configured
            $this->sendClosureMessage($ticket);

            Log::info("Ticket {$ticket->id} auto-closed due to inactivity");
        } catch (\Exception $e) {
            Log::error("Failed to auto-close ticket {$ticket->id}: " . $e->getMessage());
        }
    }

    /**
     * Update ticket activity timestamp
     */
    public function updateTicketActivity(WhatsappTicket $ticket): void
    {
        try {
            $ticket->update([
                'last_activity_at' => now(),
                'auto_close_scheduled_at' => null
            ]);

            // If ticket was on hold due to inactivity, reactivate it
            if ($ticket->status === 'on_hold' && $ticket->auto_close_scheduled_at) {
                $ticket->update(['status' => 'in_progress']);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update ticket activity for ticket {$ticket->id}: " . $e->getMessage());
        }
    }

    /**
     * Process new ticket automations
     */
    public function processNewTicket(WhatsappTicket $ticket): void
    {
        try {
            // Auto-assign if configured
            $this->autoAssignTicket($ticket);

            // Send welcome message if configured
            $this->sendWelcomeMessage($ticket);

            // Update activity timestamp
            $this->updateTicketActivity($ticket);
        } catch (\Exception $e) {
            Log::error("Failed to process new ticket {$ticket->id}: " . $e->getMessage());
        }
    }
}

