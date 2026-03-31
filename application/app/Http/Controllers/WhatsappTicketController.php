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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsappTicketController extends Controller
{
    protected $automationService;
    protected $ticketService;

    public function __construct()
    {
        $this->automationService = new WhatsappAutomationService();
        $this->ticketService = new WhatsappTicketService();
    }

    /**
     * Display the WhatsApp dashboard
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

    // (Removed) Enhanced WhatsApp dashboard

    // (Removed) Modern WhatsApp dashboard

    /**
     * Display a listing of WhatsApp tickets
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['status', 'channel', 'agent_id', 'search', 'start_date', 'end_date']);
            
            // Get date range from request
            $dateRange = $this->getDateRange($request);
            $filters['date_range'] = $dateRange;
            
            // Get tickets with better error handling
            $tickets = $this->ticketService->getPaginatedTickets($filters);
            $kpis = $this->ticketService->getKPIs();
            $agents = $this->ticketService->getAvailableAgents();
            
            // Log debugging information
            \Log::info("WhatsApp tickets index - Retrieved {$tickets->count()} tickets from {$tickets->total()} total");
            
            return view('whatsapp.tickets.index', compact('tickets', 'kpis', 'agents', 'dateRange', 'filters'));
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tickets index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return view('whatsapp.tickets.index', [
                'tickets' => $this->ticketService->getPaginatedTickets(),
                'kpis' => $this->ticketService->getKPIs(),
                'agents' => collect([]),
                'dateRange' => ['start' => now()->subDays(30)->format('Y-m-d'), 'end' => now()->format('Y-m-d')],
                'filters' => [],
                'error' => 'Error loading tickets: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get date range from request
     */
    private function getDateRange(Request $request): array
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        return [
            'start' => $startDate,
            'end' => $endDate
        ];
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        return view('whatsapp.tickets.create');
    }

    /**
     * Store a newly created ticket
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'contact_name' => 'required|string|max:255',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'priority' => 'required|in:low,medium,high,urgent',
                'category' => 'nullable|string|max:255',
                'channel' => 'required|in:whatsapp,email',
                'initial_message' => 'required|string',
            ]);

            Log::info('Creating WhatsApp ticket', [
                'contact_name' => $request->contact_name,
                'contact_phone' => $request->contact_phone,
                'subject' => $request->subject,
                'channel' => $request->channel
            ]);

            // Prepare ticket data
            $data = [
                'tenant_id' => app('currentTenant')->id ?? 1,
                'contact_name' => $request->contact_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'subject' => $request->subject,
                'status' => 'open', // Set default status
            ];

            // Add optional fields if columns exist
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'priority')) {
                $data['priority'] = $request->priority;
            }
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'category')) {
                $data['category'] = $request->category;
            }
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'channel')) {
                $data['channel'] = $request->channel;
            }
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'opened_at')) {
                $data['opened_at'] = now();
            }
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'tags')) {
                $data['tags'] = json_encode($request->tags ?? []);
            }

            // Conditionally include optional foreign keys if columns exist
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'ticket_type_id') && $request->filled('ticket_type_id')) {
                $data['ticket_type_id'] = $request->ticket_type_id;
            }
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'line_config_id') && $request->filled('line_config_id')) {
                $data['line_config_id'] = $request->line_config_id;
            }
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'last_activity_at')) {
                $data['last_activity_at'] = now();
            }

            Log::info('Ticket data prepared', $data);

            // Create the ticket
            $ticket = WhatsappTicket::create($data);
            Log::info('Ticket created successfully', ['ticket_id' => $ticket->id]);

            // Create initial message
            if ($request->filled('initial_message')) {
                $messageData = [
                    'tenant_id' => $ticket->tenant_id,
                    'ticket_id' => $ticket->id,
                    'sender_type' => 'client',
                    'sender_name' => $request->contact_name,
                    'channel' => $request->channel,
                    'body' => $request->initial_message,
                    'status' => 'sent',
                ];
                
                Log::info('Creating initial message', $messageData);
                WhatsappMessage::create($messageData);
                Log::info('Initial message created successfully');
            }

            // Process automations for new ticket (with error handling)
            try {
                if ($this->automationService) {
                    $this->automationService->processNewTicket($ticket);
                    Log::info('Automation processed successfully for ticket', ['ticket_id' => $ticket->id]);
                }
            } catch (\Exception $e) {
                Log::warning('Automation processing failed', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the entire operation if automation fails
            }

            Log::info('Ticket creation completed successfully', ['ticket_id' => $ticket->id]);

            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('success', 'Ticket created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for ticket creation', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error creating WhatsApp ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating ticket: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ticket
     */
    public function show(WhatsappTicket $ticket)
    {
        // Load the ticket with its relationships
        $ticket->load(['agent', 'messages.sender', 'ticketType']);
        $messages = $ticket->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        return view('whatsapp.tickets.show', compact('ticket', 'messages'));
    }

    /**
     * Show the form for editing the specified ticket
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
     * Update the specified ticket
     */
    public function update(Request $request, WhatsappTicket $ticket)
    {
        $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'status' => 'required|in:open,in_progress,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'channel' => 'required|in:whatsapp,email',
            'category' => 'nullable|string|max:255',
            'agent_id' => 'nullable|exists:users,id',
            'ticket_type_id' => 'nullable|exists:whatsapp_ticket_types,id',
            'internal_notes' => 'nullable|string',
        ]);

        // Prepare update data with column existence checks
        $updateData = [];
        
        // Always update these fields if they exist in the request
        $alwaysUpdateFields = ['contact_name', 'contact_email', 'contact_phone', 'subject', 'status', 'priority', 'channel', 'agent_id', 'internal_notes'];
        foreach ($alwaysUpdateFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }
        
        // Conditionally update fields that may not exist in the database
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'category') && $request->has('category')) {
            $updateData['category'] = $request->input('category');
        }
        
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'ticket_type_id') && $request->has('ticket_type_id')) {
            $updateData['ticket_type_id'] = $request->input('ticket_type_id');
        }
        
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'line_config_id') && $request->has('line_config_id')) {
            $updateData['line_config_id'] = $request->input('line_config_id');
        }
        
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'last_activity_at')) {
            $updateData['last_activity_at'] = now();
        }

        // Update the ticket
        $ticket->update($updateData);

        // Invalidate KPI cache
        $this->ticketService->clearKpiCacheForTenant($ticket->tenant_id);
        
        return redirect()->route('whatsapp.tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully!');
    }

    /**
     * Remove the specified ticket
     */
    public function destroy(WhatsappTicket $ticket)
    {
        $ticket->delete();
        $this->ticketService->clearKpiCacheForTenant($ticket->tenant_id);
        return redirect()->route('whatsapp.tickets.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    /**
     * Send a message for a ticket
     */
    public function sendMessage(Request $request, WhatsappTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'channel' => 'required|in:whatsapp,email',
        ]);

        // Get authenticated user info safely
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'You must be logged in to send messages.');
        }

        $message = WhatsappMessage::create([
            'tenant_id' => $ticket->tenant_id,
            'ticket_id' => $ticket->id,
            'sender_type' => 'agent',
            'sender_id' => $user->id,
            'sender_name' => $user->first_name . ' ' . $user->last_name,
            'channel' => $request->channel,
            'body' => $request->message,
            'status' => 'sent',
        ]);

        // Update ticket status if it was closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'in_progress']);
        }

        // Set first response time if this is the first agent response
        if (!$ticket->first_response_at && Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'first_response_at')) {
            $ticket->update(['first_response_at' => now()]);
        }

        // Update ticket activity
        $this->automationService->updateTicketActivity($ticket);

        // Send message via the specified channel
        try {
            $this->sendMessageViaChannel($request->channel, $request->message, $ticket);
            
            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('success', 'Message sent successfully!');
        } catch (\Exception $e) {
            // If message sending fails, still save the message but mark it as failed
            $message->update(['status' => 'failed']);
            
            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('error', 'Message saved but failed to send: ' . $e->getMessage());
        }
    }

    /**
     * Assign ticket to an agent
     */
    public function assign(Request $request, WhatsappTicket $ticket)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
        ]);

        $ticket->update([
            'agent_id' => $request->agent_id,
            'status' => 'in_progress',
        ]);

        $this->ticketService->clearKpiCacheForTenant($ticket->tenant_id);
        return redirect()->route('whatsapp.tickets.show', $ticket)
            ->with('success', 'Ticket assigned successfully!');
    }

    /**
     * Close a ticket
     */
    public function close(WhatsappTicket $ticket)
    {
        $updateData = ['status' => 'closed'];
        
        // Only update these fields if the columns exist
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'closed_at')) {
            $updateData['closed_at'] = now();
        }
        
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'auto_close_scheduled_at')) {
            $updateData['auto_close_scheduled_at'] = null;
        }
        
        $ticket->update($updateData);

        // Send closure message if configured
        $this->automationService->sendClosureMessage($ticket);
        $this->ticketService->clearKpiCacheForTenant($ticket->tenant_id);

        return redirect()->route('whatsapp.tickets.show', $ticket)
            ->with('success', 'Ticket closed successfully!');
    }

    /**
     * Put ticket on hold
     */
    public function putOnHold(WhatsappTicket $ticket)
    {
        $updateData = ['status' => 'on_hold'];
        
        // Only update this field if the column exists
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'auto_close_scheduled_at')) {
            $updateData['auto_close_scheduled_at'] = null;
        }
        
        $ticket->update($updateData);

        $this->ticketService->clearKpiCacheForTenant($ticket->tenant_id);
        return redirect()->route('whatsapp.tickets.show', $ticket)
            ->with('success', 'Ticket put on hold successfully!');
    }

    /**
     * Reopen a closed ticket
     */
    public function reopen(WhatsappTicket $ticket)
    {
        $updateData = ['status' => 'in_progress'];
        
        // Only update these fields if the columns exist
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'closed_at')) {
            $updateData['closed_at'] = null;
        }
        
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'auto_close_scheduled_at')) {
            $updateData['auto_close_scheduled_at'] = null;
        }
        
        $ticket->update($updateData);

        $this->ticketService->clearKpiCacheForTenant($ticket->tenant_id);
        return redirect()->route('whatsapp.tickets.show', $ticket)
            ->with('success', 'Ticket reopened successfully!');
    }

    /**
     * Update ticket type
     */
    public function updateTicketType(Request $request, WhatsappTicket $ticket)
    {
        if (!Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'ticket_type_id')) {
            return redirect()->route('whatsapp.tickets.show', $ticket)
                ->with('error', 'Ticket types are not supported in the current schema.');
        }

        $request->validate([
            'ticket_type_id' => 'nullable|exists:whatsapp_ticket_types,id'
        ]);

        $ticket->update([
            'ticket_type_id' => $request->ticket_type_id
        ]);

        $this->ticketService->clearKpiCacheForTenant($ticket->tenant_id);
        return redirect()->route('whatsapp.tickets.show', $ticket)
            ->with('success', 'Ticket type updated successfully!');
    }

    /**
     * Update ticket tags
     */
    public function updateTags(Request $request, WhatsappTicket $ticket)
    {
        $request->validate([
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:whatsapp_tags,id'
        ]);

        // Sync tags (this will replace existing tags)
        $ticket->ticketTags()->sync($request->tag_ids ?? []);

        $this->ticketService->clearKpiCacheForTenant($ticket->tenant_id);
        return redirect()->route('whatsapp.tickets.show', $ticket)
            ->with('success', 'Ticket tags updated successfully!');
    }

    /**
     * Get KPIs for the dashboard
     */
    public function getKPIs()
    {
        return $this->ticketService->getKPIs();
    }

    /**
     * Send message via the specified channel
     */
    private function sendMessageViaChannel($channel, $message, $ticket)
    {
        try {
            if ($channel === 'whatsapp') {
                $this->sendWhatsAppMessage($message, $ticket);
            } elseif ($channel === 'email') {
                $this->sendEmailMessage($message, $ticket);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send message via {$channel}: " . $e->getMessage());
            throw new \Exception("Failed to send message via {$channel}: " . $e->getMessage());
        }
    }

    /**
     * Send WhatsApp message via API
     */
    private function sendWhatsAppMessage($message, $ticket)
    {
        // Get active WhatsApp connection for this tenant
        $connection = \App\Models\WhatsappConnection::where('tenant_id', $ticket->tenant_id)
            ->where('status', 'connected')
            ->first();

        if (!$connection) {
            Log::warning("No active WhatsApp connection found for tenant {$ticket->tenant_id}");
            return;
        }

        // Check if we have WhatsApp API credentials
        if (empty($connection->connection_data) || !isset($connection->connection_data['api_key'])) {
            Log::warning("WhatsApp API credentials not configured for connection {$connection->id}");
            return;
        }

        try {
            // Try to send via WhatsApp Business API
            $response = $this->sendViaWhatsAppAPI($connection, $ticket->contact_phone, $message);
            
            if ($response['success']) {
                Log::info("WhatsApp message sent successfully to {$ticket->contact_phone}");
                // Update the message status to sent
                $this->updateMessageStatus($message, 'sent', $response['message_id'] ?? null);
            } else {
                Log::error("Failed to send WhatsApp message: " . $response['error']);
                throw new \Exception("WhatsApp API error: " . $response['error']);
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp API error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send email message
     */
    private function sendEmailMessage($message, $ticket)
    {
        if (empty($ticket->contact_email)) {
            Log::warning("No email address found for ticket {$ticket->id}");
            return;
        }

        try {
            // Create email data
            $emailData = [
                'ticket_id' => $ticket->id,
                'subject' => "Re: {$ticket->subject}",
                'message' => $message,
                'contact_name' => $ticket->contact_name,
                'agent_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'ticket_url' => route('whatsapp.tickets.show', $ticket)
            ];

            // Send email using Laravel's mail system
            Mail::to($ticket->contact_email)->send(new \App\Mail\WhatsappTicketReply($emailData));
            
            Log::info("Email message sent successfully to {$ticket->contact_email}");
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            throw new \Exception("Failed to send email: " . $e->getMessage());
        }
    }

    /**
     * Send message via WhatsApp Business API
     */
    private function sendViaWhatsAppAPI($connection, $phoneNumber, $message)
    {
        // Remove any non-numeric characters from phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add country code if not present (assuming +1 for US, adjust as needed)
        if (!str_starts_with($cleanPhone, '1') && strlen($cleanPhone) === 10) {
            $cleanPhone = '1' . $cleanPhone;
        }
        
        // Add + prefix
        $cleanPhone = '+' . $cleanPhone;

        try {
            // Try different WhatsApp API providers
            if ($connection->connection_type === 'twilio') {
                return $this->sendViaTwilio($connection, $cleanPhone, $message);
            } elseif ($connection->connection_type === '360dialog') {
                return $this->sendVia360Dialog($connection, $cleanPhone, $message);
            } else {
                // Default to generic WhatsApp Business API
                return $this->sendViaGenericWhatsAppAPI($connection, $cleanPhone, $message);
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update message status and external message ID
     */
    private function updateMessageStatus($message, $status, $externalMessageId = null)
    {
        try {
            $updateData = ['status' => $status];
            
            if ($externalMessageId) {
                $updateData['message_id'] = $externalMessageId;
            }
            
            $message->update($updateData);
        } catch (\Exception $e) {
            Log::error("Failed to update message status: " . $e->getMessage());
        }
    }

    /**
     * Send via Twilio WhatsApp API
     */
    private function sendViaTwilio($connection, $phoneNumber, $message)
    {
        $accountSid = $connection->connection_data['api_key'] ?? '';
        $authToken = $connection->connection_data['api_secret'] ?? '';
        $fromNumber = $connection->phone_number;

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";
        
        $response = Http::withBasicAuth($accountSid, $authToken)
            ->post($url, [
                'From' => "whatsapp:{$fromNumber}",
                'To' => "whatsapp:{$phoneNumber}",
                'Body' => $message
            ]);

        if ($response->successful()) {
            return ['success' => true, 'message_id' => $response->json('sid')];
        } else {
            return ['success' => false, 'error' => $response->body()];
        }
    }

    /**
     * Send via 360Dialog WhatsApp API
     */
    private function sendVia360Dialog($connection, $phoneNumber, $message)
    {
        $apiKey = $connection->connection_data['api_key'] ?? '';
        $url = "https://waba-v2.360dialog.io/messages";
        
        $response = Http::withHeaders([
            'D360-API-KEY' => $apiKey,
            'Content-Type' => 'application/json'
        ])->post($url, [
            'recipient_type' => 'individual',
            'to' => $phoneNumber,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ]);

        if ($response->successful()) {
            return ['success' => true, 'message_id' => $response->json('messages.0.id')];
        } else {
            return ['success' => false, 'error' => $response->body()];
        }
    }

    /**
     * Send via generic WhatsApp Business API
     */
    private function sendViaGenericWhatsAppAPI($connection, $phoneNumber, $message)
    {
        $apiKey = $connection->connection_data['api_key'] ?? '';
        $apiUrl = $connection->connection_data['api_url'] ?? 'https://graph.facebook.com/v17.0';
        $phoneNumberId = $connection->connection_data['phone_number_id'] ?? '';
        
        $url = "{$apiUrl}/{$phoneNumberId}/messages";
        
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json'
        ])->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $phoneNumber,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ]);

        if ($response->successful()) {
            return ['success' => true, 'message_id' => $response->json('messages.0.id')];
        } else {
            return ['success' => false, 'error' => $response->body()];
        }
    }
}
