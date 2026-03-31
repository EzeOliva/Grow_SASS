<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappTicket;
use App\Models\WhatsappMessage;
use App\Models\WhatsappTicketType;
use App\Models\WhatsappTag;
use App\Models\User;
use App\Services\WhatsappAutomationService;
use App\Services\WhatsappTicketService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @fileoverview Optimized WhatsApp Ticket Controller - Handles ticket operations with service layer
 * @description Thin controller that delegates business logic to services and focuses on HTTP concerns
 */
class WhatsappTicketControllerOptimized extends Controller
{
    protected $automationService;
    protected $ticketService;

    public function __construct()
    {
        $this->automationService = new WhatsappAutomationService();
        $this->ticketService = new WhatsappTicketService();
    }

    /**
     * @description Display the WhatsApp dashboard with KPIs
     */
    public function dashboard()
    {
        try {
            $kpis = $this->ticketService->getKPIs();
            return view('whatsapp.dashboard.index', compact('kpis'));
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp dashboard: ' . $e->getMessage());
            return view('whatsapp.dashboard.index', [
                'kpis' => $this->ticketService->getDefaultKPIs(),
                'error' => 'Error loading dashboard data'
            ]);
        }
    }

    /**
     * @description Display a listing of WhatsApp tickets with filters
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['status', 'channel', 'agent_id', 'search']);
            
            $tickets = $this->ticketService->getPaginatedTickets($filters);
            $kpis = $this->ticketService->getKPIs();
            $agents = $this->ticketService->getAvailableAgents();
            
            return view('whatsapp.tickets.index', compact('tickets', 'kpis', 'agents'));
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tickets index: ' . $e->getMessage());
            
            return view('whatsapp.tickets.index', [
                'tickets' => $this->ticketService->getPaginatedTickets(),
                'kpis' => $this->ticketService->getKPIs(),
                'agents' => collect([]),
                'error' => 'Error loading tickets: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @description Show the form for creating a new ticket
     */
    public function create()
    {
        try {
            $agents = $this->ticketService->getAvailableAgents();
            $ticketTypes = $this->ticketService->getTicketTypes();
            $tags = $this->ticketService->getTags();
            
            return view('whatsapp.tickets.create', compact('agents', 'ticketTypes', 'tags'));
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp ticket create: ' . $e->getMessage());
            return redirect()->route('whatsapp.tickets.index')
                ->with('error', 'Error loading ticket form: ' . $e->getMessage());
        }
    }

    /**
     * @description Store a newly created ticket
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'contact_name' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|max:20',
                'subject' => 'required|string|max:500',
                'description' => 'required|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'channel' => 'required|in:whatsapp,email,phone',
                'agent_id' => 'nullable|exists:users,id',
                'ticket_type_id' => 'nullable|exists:whatsapp_ticket_types,id'
            ]);

            $ticketData = $request->only([
                'contact_name', 'contact_email', 'contact_phone', 'subject', 
                'description', 'priority', 'channel', 'agent_id', 'ticket_type_id'
            ]);

            $ticket = $this->ticketService->createTicket($ticketData);

            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('success', 'Ticket created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating WhatsApp ticket: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating ticket: ' . $e->getMessage());
        }
    }

    /**
     * @description Display the specified ticket
     */
    public function show(WhatsappTicket $ticket)
    {
        try {
            $ticket->load(['agent', 'messages.sender', 'ticketType', 'tags']);
            
            return view('whatsapp.tickets.show', compact('ticket'));
        } catch (\Exception $e) {
            \Log::error('Error showing WhatsApp ticket: ' . $e->getMessage());
            return redirect()->route('whatsapp.tickets.index')
                ->with('error', 'Error loading ticket: ' . $e->getMessage());
        }
    }

    /**
     * @description Show the form for editing the specified ticket
     */
    public function edit(WhatsappTicket $ticket)
    {
        try {
            // Load the ticket with its relationships
            $ticket->load(['agent', 'messages.sender']);
            
            // Get data using services
            $agents = $this->ticketService->getAvailableAgents();
            $ticketTypes = $this->ticketService->getTicketTypes();
            $tags = $this->ticketService->getTags();
            
            return view('whatsapp.tickets.edit', compact('ticket', 'agents', 'ticketTypes', 'tags'));
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp ticket edit: ' . $e->getMessage());
            return redirect()->route('whatsapp.tickets.index')
                ->with('error', 'Error loading ticket data: ' . $e->getMessage());
        }
    }

    /**
     * @description Update the specified ticket
     */
    public function update(Request $request, WhatsappTicket $ticket)
    {
        try {
            $request->validate([
                'status' => 'required|in:open,in_progress,closed',
                'priority' => 'required|in:low,medium,high,urgent',
                'category' => 'nullable|string|max:255',
                'internal_notes' => 'nullable|string',
            ]);

            $ticket->update($request->only(['status', 'priority', 'category', 'internal_notes']));

            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('success', 'Ticket updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating WhatsApp ticket: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating ticket: ' . $e->getMessage());
        }
    }

    /**
     * @description Remove the specified ticket
     */
    public function destroy(WhatsappTicket $ticket)
    {
        try {
            $ticket->delete();
            return redirect()->route('whatsapp.tickets.index')
                ->with('success', 'Ticket deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting WhatsApp ticket: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting ticket: ' . $e->getMessage());
        }
    }

    /**
     * @description Send a message for a ticket
     */
    public function sendMessage(Request $request, WhatsappTicket $ticket)
    {
        try {
            $request->validate([
                'message' => 'required|string',
                'channel' => 'required|in:whatsapp,email',
            ]);

            // Get authenticated user info safely
            $user = Auth::user();
            if (!$user) {
                return redirect()->back()->with('error', 'You must be logged in to send messages.');
            }

            $messageData = [
                'sender_id' => $user->id,
                'sender_name' => $user->first_name . ' ' . $user->last_name,
                'channel' => $request->channel,
                'body' => $request->message,
            ];

            $message = $this->ticketService->sendMessage($ticket, $messageData);

            // Update ticket activity
            $this->automationService->updateTicketActivity($ticket);

            // TODO: Implement actual message sending via WhatsApp/Email
            $this->sendMessageViaChannel($request->channel, $request->message, $ticket);

            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('success', 'Message sent successfully!');
        } catch (\Exception $e) {
            \Log::error('Error sending WhatsApp message: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error sending message: ' . $e->getMessage());
        }
    }

    /**
     * @description Assign ticket to an agent
     */
    public function assign(Request $request, WhatsappTicket $ticket)
    {
        try {
            $request->validate([
                'agent_id' => 'required|exists:users,id'
            ]);

            $success = $this->ticketService->assignTicket($ticket, $request->agent_id);

            if ($success) {
                return redirect()->route('whatsapp.tickets.show', $ticket)
                    ->with('success', 'Ticket assigned successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to assign ticket.');
            }
        } catch (\Exception $e) {
            \Log::error('Error assigning WhatsApp ticket: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error assigning ticket: ' . $e->getMessage());
        }
    }

    /**
     * @description Close a ticket
     */
    public function close(Request $request, WhatsappTicket $ticket)
    {
        try {
            $success = $this->ticketService->updateTicketStatus($ticket, 'closed');

            if ($success) {
                return redirect()->route('whatsapp.tickets.show', $ticket)
                    ->with('success', 'Ticket closed successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to close ticket.');
            }
        } catch (\Exception $e) {
            \Log::error('Error closing WhatsApp ticket: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error closing ticket: ' . $e->getMessage());
        }
    }

    /**
     * @description Reopen a closed ticket
     */
    public function reopen(Request $request, WhatsappTicket $ticket)
    {
        try {
            $success = $this->ticketService->updateTicketStatus($ticket, 'in_progress');

            if ($success) {
                return redirect()->route('whatsapp.tickets.show', $ticket)
                    ->with('success', 'Ticket reopened successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to reopen ticket.');
            }
        } catch (\Exception $e) {
            \Log::error('Error reopening WhatsApp ticket: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error reopening ticket: ' . $e->getMessage());
        }
    }

    /**
     * @description Put ticket on hold
     */
    public function putOnHold(Request $request, WhatsappTicket $ticket)
    {
        try {
            $success = $this->ticketService->updateTicketStatus($ticket, 'on_hold');

            if ($success) {
                return redirect()->route('whatsapp.tickets.show', $ticket)
                    ->with('success', 'Ticket put on hold successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to put ticket on hold.');
            }
        } catch (\Exception $e) {
            \Log::error('Error putting WhatsApp ticket on hold: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error putting ticket on hold: ' . $e->getMessage());
        }
    }

    /**
     * @description Update ticket type
     */
    public function updateTicketType(Request $request, WhatsappTicket $ticket)
    {
        try {
            $request->validate([
                'ticket_type_id' => 'required|exists:whatsapp_ticket_types,id'
            ]);

            $ticket->update([
                'ticket_type_id' => $request->ticket_type_id
            ]);

            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('success', 'Ticket type updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating WhatsApp ticket type: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating ticket type: ' . $e->getMessage());
        }
    }

    /**
     * @description Update ticket tags
     */
    public function updateTags(Request $request, WhatsappTicket $ticket)
    {
        try {
            $request->validate([
                'tag_ids' => 'nullable|array',
                'tag_ids.*' => 'exists:whatsapp_tags,id'
            ]);

            // Sync tags (this will replace existing tags)
            $ticket->ticketTags()->sync($request->tag_ids ?? []);

            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('success', 'Ticket tags updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating WhatsApp ticket tags: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating ticket tags: ' . $e->getMessage());
        }
    }

    /**
     * @description Send message via the specified channel
     */
    private function sendMessageViaChannel($channel, $message, $ticket)
    {
        // TODO: Implement actual message sending
        // This is a placeholder for WhatsApp/Email integration
        
        if ($channel === 'whatsapp') {
            // TODO: Send via WhatsApp API (Twilio, 360dialog, etc.)
            \Log::info("WhatsApp message sent: {$message}");
        } elseif ($channel === 'email') {
            // TODO: Send via email service
            \Log::info("Email message sent: {$message}");
        }
    }
}
