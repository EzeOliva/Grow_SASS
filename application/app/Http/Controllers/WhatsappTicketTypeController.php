<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappTicketType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @fileoverview WhatsApp Ticket Type Controller
 * @description Manages ticket types for categorization with editable lists per company
 */
class WhatsappTicketTypeController extends Controller
{
    /**
     * Display a listing of ticket types
     */
    public function index(Request $request)
    {
        $query = WhatsappTicketType::where('tenant_id', app('currentTenant')->id ?? 1); // TODO: Get from current tenant context

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $ticketTypes = $query->with(['creator'])
            ->ordered()
            ->paginate(20);

        return view('whatsapp.ticket-types.index', compact('ticketTypes'));
    }

    /**
     * Show the form for creating a new ticket type
     */
    public function create()
    {
        $colors = [
            '#6c757d' => 'Gray',
            '#007bff' => 'Blue',
            '#28a745' => 'Green',
            '#ffc107' => 'Yellow',
            '#dc3545' => 'Red',
            '#fd7e14' => 'Orange',
            '#6f42c1' => 'Purple',
            '#e83e8c' => 'Pink',
            '#20c997' => 'Teal',
            '#17a2b8' => 'Cyan'
        ];

        return view('whatsapp.ticket-types.create', compact('colors'));
    }

    /**
     * Store a newly created ticket type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            // Check for duplicate ticket type name
            $existingType = WhatsappTicketType::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('name', $request->name)
                ->first();

            if ($existingType) {
                return back()->with('error', 'A ticket type with this name already exists.')->withInput();
            }

            // Get next sort order if not specified
            if (!$request->filled('sort_order')) {
                $maxSortOrder = WhatsappTicketType::where('tenant_id', app('currentTenant')->id ?? 1)->max('sort_order') ?? 0;
                $request->merge(['sort_order' => $maxSortOrder + 10]);
            }

            $ticketType = WhatsappTicketType::create([
                'tenant_id' => app('currentTenant')->id ?? 1, // TODO: Get from current tenant context
                'name' => $request->name,
                'color' => $request->color,
                'description' => $request->description,
                'sort_order' => $request->sort_order,
                'is_active' => true,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('whatsapp.ticket-types.show', $ticketType)
                ->with('success', 'Ticket type created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating ticket type: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified ticket type
     */
    public function show(WhatsappTicketType $ticketType)
    {
        $ticketType->load(['creator']);
        
        // Get recent tickets of this type
        $recentTickets = $ticketType->tickets()
            ->with(['agent', 'lineConfig'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('whatsapp.ticket-types.show', compact('ticketType', 'recentTickets'));
    }

    /**
     * Show the form for editing the specified ticket type
     */
    public function edit(WhatsappTicketType $ticketType)
    {
        $colors = [
            '#6c757d' => 'Gray',
            '#007bff' => 'Blue',
            '#28a745' => 'Green',
            '#ffc107' => 'Yellow',
            '#dc3545' => 'Red',
            '#fd7e14' => 'Orange',
            '#6f42c1' => 'Purple',
            '#e83e8c' => 'Pink',
            '#20c997' => 'Teal',
            '#17a2b8' => 'Cyan'
        ];

        return view('whatsapp.ticket-types.edit', compact('ticketType', 'colors'));
    }

    /**
     * Update the specified ticket type
     */
    public function update(Request $request, WhatsappTicketType $ticketType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            // Check for duplicate ticket type name (excluding current type)
            $existingType = WhatsappTicketType::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('name', $request->name)
                ->where('id', '!=', $ticketType->id)
                ->first();

            if ($existingType) {
                return back()->with('error', 'A ticket type with this name already exists.')->withInput();
            }

            $ticketType->update([
                'name' => $request->name,
                'color' => $request->color,
                'description' => $request->description,
                'sort_order' => $request->sort_order
            ]);

            return redirect()->route('whatsapp.ticket-types.show', $ticketType)
                ->with('success', 'Ticket type updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating ticket type: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified ticket type
     */
    public function destroy(WhatsappTicketType $ticketType)
    {
        try {
            if (!$ticketType->canBeDeleted()) {
                return back()->with('error', 'Cannot delete ticket type. It is currently used by tickets. Please reassign or delete all tickets first.');
            }

            $ticketType->delete();

            return redirect()->route('whatsapp.ticket-types.index')
                ->with('success', 'Ticket type deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Toggle ticket type active status
     */
    public function toggleStatus(WhatsappTicketType $ticketType)
    {
        try {
            $ticketType->update(['is_active' => !$ticketType->is_active]);

            $status = $ticketType->is_active ? 'activated' : 'deactivated';
            return back()->with('success', "Ticket type {$status} successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error toggling ticket type status: ' . $e->getMessage());
        }
    }

    /**
     * Get ticket types for AJAX requests (used in forms)
     */
    public function getTicketTypes(Request $request)
    {
        $search = $request->get('search', '');

        $query = WhatsappTicketType::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where('is_active', true);

        // Apply search
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $ticketTypes = $query->select('id', 'name', 'color', 'description')
            ->ordered()
            ->limit(20)
            ->get();

        return response()->json($ticketTypes);
    }

    /**
     * Update sort order for ticket types
     */
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'ticket_type_ids' => 'required|array',
            'ticket_type_ids.*' => 'exists:whatsapp_ticket_types,id'
        ]);

        try {
            foreach ($request->ticket_type_ids as $index => $id) {
                WhatsappTicketType::where('id', $id)
                    ->where('tenant_id', app('currentTenant')->id ?? 1)
                    ->update(['sort_order' => ($index + 1) * 10]);
            }

            return response()->json(['success' => true, 'message' => 'Sort order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating sort order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk operations on ticket types
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ticket_type_ids' => 'required|array',
            'ticket_type_ids.*' => 'exists:whatsapp_ticket_types,id'
        ]);

        try {
            $ticketTypes = WhatsappTicketType::whereIn('id', $request->ticket_type_ids)
                ->where('tenant_id', app('currentTenant')->id ?? 1)
                ->get();

            $successCount = 0;
            $errorCount = 0;

            foreach ($ticketTypes as $ticketType) {
                try {
                    switch ($request->action) {
                        case 'activate':
                            $ticketType->update(['is_active' => true]);
                            $successCount++;
                            break;
                        case 'deactivate':
                            $ticketType->update(['is_active' => false]);
                            $successCount++;
                            break;
                        case 'delete':
                            if ($ticketType->canBeDeleted()) {
                                $ticketType->delete();
                                $successCount++;
                            } else {
                                $errorCount++;
                            }
                            break;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }

            $message = "Bulk action completed. {$successCount} ticket types processed successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} ticket types could not be processed.";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    /**
     * Get ticket type statistics
     */
    public function getStats(WhatsappTicketType $ticketType)
    {
        try {
            $stats = [
                'total_tickets' => $ticketType->ticket_count,
                'open_tickets' => $ticketType->open_ticket_count,
                'closed_tickets' => $ticketType->closed_ticket_count,
                'avg_response_time' => $this->calculateAverageResponseTime($ticketType),
                'avg_resolution_time' => $this->calculateAverageResolutionTime($ticketType)
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error getting statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate average first response time for a ticket type
     */
    private function calculateAverageResponseTime(WhatsappTicketType $ticketType): float
    {
        $result = $ticketType->tickets()
            ->whereNotNull('first_response_at')
            ->whereNotNull('opened_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, first_response_at)) as avg_response_time')
            ->first();

        return round($result->avg_response_time ?? 0, 2);
    }

    /**
     * Calculate average resolution time for a ticket type
     */
    private function calculateAverageResolutionTime(WhatsappTicketType $ticketType): float
    {
        $result = $ticketType->tickets()
            ->whereNotNull('closed_at')
            ->whereNotNull('opened_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, closed_at)) as avg_resolution_time')
            ->first();

        return round($result->avg_resolution_time ?? 0, 2);
    }
}

