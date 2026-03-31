<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappTickList;
use App\Models\WhatsappTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WhatsappTickListController extends Controller
{
    /**
     * Display a listing of tick lists for a specific ticket
     */
    public function index(Request $request, $ticketId)
    {
        try {
            $ticket = WhatsappTicket::findOrFail($ticketId);
            
            $query = WhatsappTickList::where('ticket_id', $ticketId)
                ->where('tenant_id', app('currentTenant')->id ?? 1); // TODO: Get from current tenant context
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }
            
            if ($request->filled('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Paginate with 5 items per page as requested
            $tickLists = $query->with(['creator', 'assignee'])
                ->orderBy('priority', 'desc')
                ->orderBy('due_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
            
            // Get agents for the filter dropdown
            $agents = \App\Models\User::where('type', 'team')
                ->select('id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->get();
            
            return view('whatsapp.tickets.tick-lists.index', compact('ticket', 'tickLists', 'agents'));
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tick lists index: ' . $e->getMessage());
            return back()->with('error', 'Error loading tick lists: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new tick list item
     */
    public function create($ticketId)
    {
        try {
            $ticket = WhatsappTicket::findOrFail($ticketId);
            
            // Get agents for assignment
            $agents = \App\Models\User::where('type', 'team')
                ->select('id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->get();
            
            return view('whatsapp.tickets.tick-lists.create', compact('ticket', 'agents'));
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tick list create: ' . $e->getMessage());
            return back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created tick list item
     */
    public function store(Request $request, $ticketId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|integer|between:1,4',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        try {
            $ticket = WhatsappTicket::findOrFail($ticketId);
            
            $tickList = WhatsappTickList::create([
                'tenant_id' => app('currentTenant')->id ?? 1, // TODO: Get from current tenant context
                'ticket_id' => $ticketId,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'assigned_to' => $request->assigned_to,
                'due_date' => $request->due_date,
                'created_by' => Auth::id(),
                'status' => 'pending'
            ]);

            return redirect()->route('whatsapp.tickets.tick-lists.index', $ticketId)
                ->with('success', 'Tick list item created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tick list store: ' . $e->getMessage());
            return back()->with('error', 'Error creating tick list item: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified tick list item
     */
    public function edit($ticketId, $tickListId)
    {
        try {
            $ticket = WhatsappTicket::findOrFail($ticketId);
            $tickList = WhatsappTickList::where('ticket_id', $ticketId)
                ->where('id', $tickListId)
                ->firstOrFail();
            
            // Get agents for assignment
            $agents = \App\Models\User::where('type', 'team')
                ->select('id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->get();
            
            return view('whatsapp.tickets.tick-lists.edit', compact('ticket', 'tickList', 'agents'));
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tick list edit: ' . $e->getMessage());
            return back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified tick list item
     */
    public function update(Request $request, $ticketId, $tickListId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|integer|between:1,4',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        try {
            $tickList = WhatsappTickList::where('ticket_id', $ticketId)
                ->where('id', $tickListId)
                ->firstOrFail();
            
            $tickList->update([
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'assigned_to' => $request->assigned_to,
                'due_date' => $request->due_date,
            ]);

            return redirect()->route('whatsapp.tickets.tick-lists.index', $ticketId)
                ->with('success', 'Tick list item updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tick list update: ' . $e->getMessage());
            return back()->with('error', 'Error updating tick list item: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified tick list item (DELETE functionality)
     */
    public function destroy($ticketId, $tickListId)
    {
        try {
            $tickList = WhatsappTickList::where('ticket_id', $ticketId)
                ->where('id', $tickListId)
                ->firstOrFail();
            
            $tickList->delete();

            return redirect()->route('whatsapp.tickets.tick-lists.index', $ticketId)
                ->with('success', 'Tick list item deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tick list destroy: ' . $e->getMessage());
            return back()->with('error', 'Error deleting tick list item: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of a tick list item
     */
    public function toggleStatus($ticketId, $tickListId)
    {
        try {
            $tickList = WhatsappTickList::where('ticket_id', $ticketId)
                ->where('id', $tickListId)
                ->firstOrFail();
            
            $tickList->update([
                'status' => $tickList->status === 'pending' ? 'completed' : 'pending'
            ]);

            return response()->json([
                'success' => true,
                'status' => $tickList->status,
                'message' => 'Status updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tick list toggle status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete tick list items
     */
    public function bulkDestroy(Request $request, $ticketId)
    {
        $request->validate([
            'tick_list_ids' => 'required|array',
            'tick_list_ids.*' => 'exists:whatsapp_tick_lists,id'
        ]);

        try {
            $deletedCount = WhatsappTickList::where('ticket_id', $ticketId)
                ->whereIn('id', $request->tick_list_ids)
                ->delete();

            return redirect()->route('whatsapp.tickets.tick-lists.index', $ticketId)
                ->with('success', "{$deletedCount} tick list item(s) deleted successfully!");
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp tick list bulk destroy: ' . $e->getMessage());
            return back()->with('error', 'Error deleting tick list items: ' . $e->getMessage());
        }
    }
}

