<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappTicket;
use App\Models\WhatsappTag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @fileoverview WhatsApp Contact Controller
 * @description Manages enhanced contact information including quick view, inline editing,
 * company details, and contact history
 */
class WhatsappContactController extends Controller
{
    /**
     * Display a listing of contacts
     */
    public function index(Request $request)
    {
        $query = $this->buildContactQuery($request);

        $contacts = $query->paginate(20);

        // Get available tags for filtering
        $availableTags = WhatsappTag::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where('is_active', true)
            ->whereIn('type', ['contact', 'global'])
            ->orderBy('name')
            ->get();

        // Get agents for filtering
        $agents = User::where('type', 'team')
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();

        return view('whatsapp.contacts.index', compact('contacts', 'availableTags', 'agents'));
    }

    /**
     * Build the contact query with filters
     */
    private function buildContactQuery(Request $request)
    {
        $query = DB::table('whatsapp_tickets')
            ->select([
                'contact_name',
                'contact_email',
                'contact_phone',
                'whatsapp_number',
                'company',
                DB::raw('COUNT(*) as ticket_count'),
                DB::raw('MAX(created_at) as last_contact'),
                DB::raw('MIN(created_at) as first_contact'),
                DB::raw('COUNT(CASE WHEN status = "closed" THEN 1 END) as resolved_tickets'),
                DB::raw('AVG(CASE WHEN first_response_at IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, created_at, first_response_at) END) as avg_response_time')
            ])
            ->where('tenant_id', app('currentTenant')->id ?? 1) // Get from current tenant context
            ->groupBy('contact_name', 'contact_email', 'contact_phone', 'whatsapp_number', 'company');      

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contact_name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhere('contact_phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tag')) {
            $tag = $request->tag;
            $query->whereRaw("JSON_CONTAINS(tags, '\"{$tag}\"')");
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // Order by
        $orderBy = $request->get('order_by', 'last_contact');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        return $query;
    }

    /**
     * Show contact details with history
     */
    public function show(Request $request, $contactIdentifier)
    {
        // Determine if identifier is email, phone, or name
        $tickets = WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where(function($query) use ($contactIdentifier) {
                $query->where('contact_email', $contactIdentifier)
                      ->orWhere('contact_phone', $contactIdentifier)
                      ->orWhere('contact_name', $contactIdentifier);
            })
            ->with(['messages', 'agent'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($tickets->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        // Get contact info from first ticket
        $firstTicket = $tickets->first();
        $contact = [
            'name' => $firstTicket->contact_name,
            'email' => $firstTicket->contact_email,
            'phone' => $firstTicket->contact_phone,
            'whatsapp' => $firstTicket->whatsapp_number,
            'company' => $firstTicket->company,
            'ticket_count' => $tickets->count(),
            'first_contact' => $tickets->last()->created_at,
            'last_contact' => $tickets->first()->created_at,
            'resolved_tickets' => $tickets->where('status', 'closed')->count(),
            'avg_response_time' => $tickets->whereNotNull('first_response_at')->avg(function($ticket) {
                return $ticket->created_at->diffInMinutes($ticket->first_response_at);
            })
        ];

        // Get available tags for this contact
        $availableTags = WhatsappTag::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where('is_active', true)
            ->whereIn('type', ['contact', 'global'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'contact' => $contact,
            'tickets' => $tickets,
            'available_tags' => $availableTags
        ]);
    }

    /**
     * Update contact information
     */
    public function update(Request $request, $contactIdentifier)
    {
        $request->validate([
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255'
        ]);

        try {
            $updated = WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where(function($query) use ($contactIdentifier) {
                    $query->where('contact_email', $contactIdentifier)
                          ->orWhere('contact_phone', $contactIdentifier)
                          ->orWhere('contact_name', $contactIdentifier);
                })
                ->update([
                    'contact_name' => $request->contact_name,
                    'contact_email' => $request->contact_email,
                    'contact_phone' => $request->contact_phone,
                    'company' => $request->company
                ]);

            if ($updated > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact information updated successfully',
                    'updated_count' => $updated
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No contacts found to update'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating contact: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign tags to contact
     */
    public function assignTags(Request $request, $contactIdentifier)
    {
        $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:whatsapp_tags,id'
        ]);

        try {
            // Get all tickets for this contact
            $tickets = WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where(function($query) use ($contactIdentifier) {
                    $query->where('contact_email', $contactIdentifier)
                          ->orWhere('contact_phone', $contactIdentifier)
                          ->orWhere('contact_name', $contactIdentifier);
                })
                ->get();

            $updatedCount = 0;
            foreach ($tickets as $ticket) {
                $currentTags = $ticket->tags ?? [];

                // Add new tags
                foreach ($request->tag_ids as $tagId) {
                    $tag = WhatsappTag::find($tagId);
                    if ($tag && !in_array($tag->name, $currentTags)) {
                        $currentTags[] = $tag->name;
                    }
                }

                $ticket->update(['tags' => $currentTags]);
                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Tags assigned to {$updatedCount} tickets successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning tags: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove tags from contact
     */
    public function removeTags(Request $request, $contactIdentifier)
    {
        $request->validate([
            'tag_names' => 'required|array',
            'tag_names.*' => 'string'
        ]);

        try {
            $tickets = WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where(function($query) use ($contactIdentifier) {
                    $query->where('contact_email', $contactIdentifier)
                          ->orWhere('contact_phone', $contactIdentifier)
                          ->orWhere('contact_name', $contactIdentifier);
                })
                ->get();

            $updatedCount = 0;
            foreach ($tickets as $ticket) {
                $currentTags = $ticket->tags ?? [];

                // Remove specified tags
                $currentTags = array_diff($currentTags, $request->tag_names);

                $ticket->update(['tags' => array_values($currentTags)]);
                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Tags removed from {$updatedCount} tickets successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing tags: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contact suggestions for autocomplete
     */
    public function getSuggestions(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $contacts = DB::table('whatsapp_tickets')
            ->select([
                'contact_name',
                'contact_email',
                'contact_phone',
                'company'
            ])
            ->where('tenant_id', app('currentTenant')->id ?? 1)
            ->where(function($query) use ($search) {
                $query->where('contact_name', 'like', "%{$search}%")
                      ->orWhere('contact_email', 'like', "%{$search}%")
                      ->orWhere('contact_phone', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%");
            })
            ->groupBy('contact_name', 'contact_email', 'contact_phone', 'company')
            ->limit(10)
            ->get();

        return response()->json($contacts);
    }

    /**
     * Export contacts
     */
    public function export(Request $request)
    {
        $query = $this->buildContactQuery($request);
        $contacts = $query->get();

        // TODO: Implement CSV/Excel export
        return response()->json([
            'success' => true,
            'message' => 'Export functionality coming soon',
            'count' => $contacts->count()
        ]);
    }

    /**
     * Get contact interaction timeline
     */
    public function getTimeline(Request $request, $contactIdentifier)
    {
        $tickets = WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where(function($query) use ($contactIdentifier) {
                $query->where('contact_email', $contactIdentifier)
                      ->orWhere('contact_email', $contactIdentifier)
                      ->orWhere('contact_name', $contactIdentifier);
            })
            ->with(['messages', 'agent'])
            ->orderBy('created_at', 'desc')
            ->get();

        $timeline = [];
        foreach ($tickets as $ticket) {
            $timeline[] = [
                'type' => 'ticket_created',
                'timestamp' => $ticket->created_at,
                'title' => "Ticket #{$ticket->id} created",
                'description' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'agent' => $ticket->agent ? $ticket->agent->first_name . ' ' . $ticket->agent->last_name : null
            ];

            if ($ticket->first_response_at) {
                $timeline[] = [
                    'type' => 'first_response',
                    'timestamp' => $ticket->first_response_at,
                    'title' => "First response to ticket #{$ticket->id}",
                    'description' => "Response time: " . $ticket->created_at->diffInMinutes($ticket->first_response_at) . " minutes",
                    'status' => 'response',
                    'agent' => $ticket->agent ? $ticket->agent->first_name . ' ' . $ticket->agent->last_name : null
                ];
            }

            if ($ticket->closed_at) {
                $timeline[] = [
                    'type' => 'ticket_closed',
                    'timestamp' => $ticket->closed_at,
                    'title' => "Ticket #{$ticket->id} closed",
                    'description' => "Resolution time: " . $ticket->created_at->diffInMinutes($ticket->closed_at) . " minutes",
                    'status' => 'closed'
                ];
            }
        }

        // Sort timeline by timestamp (newest first)
        usort($timeline, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return response()->json($timeline);
    }
}
