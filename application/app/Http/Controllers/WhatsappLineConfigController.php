<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappLineConfig;
use App\Models\WhatsappConnection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @fileoverview WhatsApp Line Configuration Controller
 * @description Manages line-specific settings for WhatsApp connections including auto-assignment, 
 * welcome messages, closure messages, and inactivity handling
 */
class WhatsappLineConfigController extends Controller
{
    /**
     * Display a listing of line configurations
     */
    public function index()
    {
        $lineConfigs = WhatsappLineConfig::with(['connection', 'tenant'])
            ->where('tenant_id', app('currentTenant')->id ?? 1) // Get from current tenant context
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('whatsapp.line-configs.index', compact('lineConfigs'));
    }

    /**
     * Show the form for creating a new line configuration
     */
    public function create()
    {
        $connections = WhatsappConnection::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where('is_active', true)
            ->get();

        $agents = User::where('type', 'team')
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();

        return view('whatsapp.line-configs.create', compact('connections', 'agents'));
    }

    /**
     * Store a newly created line configuration
     */
    public function store(Request $request)
    {
        $request->validate([
            'connection_id' => 'required|exists:whatsapp_connections,id',
            'line_name' => 'required|string|max:255',
            'assignment_mode' => 'required|in:manual,auto_round_robin,auto_load_balanced',
            'auto_assign_enabled' => 'boolean',
            'welcome_message' => 'nullable|string|max:1000',
            'closure_message' => 'nullable|string|max:1000',
            'inactivity_message' => 'nullable|string|max:1000',
            'inactivity_timeout_minutes' => 'nullable|integer|min:1|max:10080', // Max 1 week
            'auto_assign_agents' => 'nullable|array',
            'auto_assign_agents.*' => 'exists:users,id',
            'routing_rules' => 'nullable|array'
        ]);

        try {
            $lineConfig = WhatsappLineConfig::create([
                'tenant_id' => app('currentTenant')->id ?? 1, // Get from current tenant context
                'connection_id' => $request->connection_id,
                'line_name' => $request->line_name,
                'assignment_mode' => $request->assignment_mode,
                'auto_assign_enabled' => $request->boolean('auto_assign_enabled'),
                'welcome_message' => $request->welcome_message,
                'closure_message' => $request->closure_message,
                'inactivity_message' => $request->inactivity_message,
                'inactivity_timeout_minutes' => $request->inactivity_timeout_minutes,
                'auto_assign_agents' => $request->auto_assign_agents,
                'routing_rules' => $request->routing_rules,
                'is_active' => true
            ]);

            return redirect()->route('whatsapp.line-configs.show', $lineConfig)
                ->with('success', 'Line configuration created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating line configuration: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified line configuration
     */
    public function show(WhatsappLineConfig $lineConfig)
    {
        $lineConfig->load(['connection', 'tenant']);
        
        // Get agent details for auto-assignment
        $agents = collect();
        if ($lineConfig->auto_assign_agents) {
            $agents = User::whereIn('id', $lineConfig->auto_assign_agents)
                ->select('id', 'first_name', 'last_name')
                ->get();
        }

        // Get recent tickets for this line (only if line_config_id column exists)
        $recentTickets = collect();
        if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'line_config_id')) {
            $recentTickets = $lineConfig->tickets()
                ->with(['agent', 'ticketType'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('whatsapp.line-configs.show', compact('lineConfig', 'agents', 'recentTickets'));
    }

    /**
     * Show the form for editing the specified line configuration
     */
    public function edit(WhatsappLineConfig $lineConfig)
    {
        $connections = WhatsappConnection::where('tenant_id', app('currentTenant')->id ?? 1)
            ->where('is_active', true)
            ->get();

        $agents = User::where('type', 'team')
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();

        return view('whatsapp.line-configs.edit', compact('lineConfig', 'connections', 'agents'));
    }

    /**
     * Update the specified line configuration
     */
    public function update(Request $request, WhatsappLineConfig $lineConfig)
    {
        $request->validate([
            'connection_id' => 'required|exists:whatsapp_connections,id',
            'line_name' => 'required|string|max:255',
            'assignment_mode' => 'required|in:manual,auto_round_robin,auto_load_balanced',
            'auto_assign_enabled' => 'boolean',
            'welcome_message' => 'nullable|string|max:1000',
            'closure_message' => 'nullable|string|max:1000',
            'inactivity_message' => 'nullable|string|max:1000',
            'inactivity_timeout_minutes' => 'nullable|integer|min:1|max:10080',
            'auto_assign_agents' => 'nullable|array',
            'auto_assign_agents.*' => 'exists:users,id',
            'routing_rules' => 'nullable|array'
        ]);

        try {
            $lineConfig->update([
                'connection_id' => $request->connection_id,
                'line_name' => $request->line_name,
                'assignment_mode' => $request->assignment_mode,
                'auto_assign_enabled' => $request->boolean('auto_assign_enabled'),
                'welcome_message' => $request->welcome_message,
                'closure_message' => $request->closure_message,
                'inactivity_message' => $request->inactivity_message,
                'inactivity_timeout_minutes' => $request->inactivity_timeout_minutes,
                'auto_assign_agents' => $request->auto_assign_agents,
                'routing_rules' => $request->routing_rules
            ]);

            return redirect()->route('whatsapp.line-configs.show', $lineConfig)
                ->with('success', 'Line configuration updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating line configuration: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified line configuration
     */
    public function destroy(WhatsappLineConfig $lineConfig)
    {
        try {
            // Check if line has active tickets (only if line_config_id column exists)
            $activeTickets = 0;
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'line_config_id')) {
                $activeTickets = $lineConfig->tickets()
                    ->whereIn('status', ['open', 'on_hold', 'in_progress'])
                    ->count();
            }

            if ($activeTickets > 0) {
                return back()->with('error', 'Cannot delete line configuration with active tickets. Please close all tickets first.');
            }

            $lineConfig->delete();

            return redirect()->route('whatsapp.line-configs.index')
                ->with('success', 'Line configuration deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting line configuration: ' . $e->getMessage());
        }
    }

    /**
     * Toggle line configuration active status
     */
    public function toggleStatus(WhatsappLineConfig $lineConfig)
    {
        try {
            $lineConfig->update(['is_active' => !$lineConfig->is_active]);

            $status = $lineConfig->is_active ? 'activated' : 'deactivated';
            return back()->with('success', "Line configuration {$status} successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error toggling line configuration status: ' . $e->getMessage());
        }
    }

    /**
     * Test auto-assignment for a line configuration
     */
    public function testAutoAssignment(WhatsappLineConfig $lineConfig)
    {
        try {
            if (!$lineConfig->isAutoAssignmentEnabled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Auto-assignment is not enabled for this line'
                ]);
            }

            $nextAgent = $lineConfig->getNextAgent();
            
            if (!$nextAgent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No agents available for auto-assignment'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Next agent for assignment: {$nextAgent->first_name} {$nextAgent->last_name}",
                'agent' => [
                    'id' => $nextAgent->id,
                    'name' => "{$nextAgent->first_name} {$nextAgent->last_name}"
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing auto-assignment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get line configuration statistics
     */
    public function getStats(WhatsappLineConfig $lineConfig)
    {
        try {
            $stats = [
                'total_tickets' => 0,
                'open_tickets' => 0,
                'on_hold_tickets' => 0,
                'in_progress_tickets' => 0,
                'closed_tickets' => 0,
                'avg_response_time' => 0,
                'avg_resolution_time' => 0
            ];
            
            // Only calculate stats if line_config_id column exists
            if (Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'line_config_id')) {
                $stats = [
                    'total_tickets' => $lineConfig->tickets()->count(),
                    'open_tickets' => $lineConfig->tickets()->where('status', 'open')->count(),
                    'on_hold_tickets' => $lineConfig->tickets()->where('status', 'on_hold')->count(),
                    'in_progress_tickets' => $lineConfig->tickets()->where('status', 'in_progress')->count(),
                    'closed_tickets' => $lineConfig->tickets()->where('status', 'closed')->count(),
                    'avg_response_time' => $this->calculateAverageResponseTime($lineConfig),
                    'avg_resolution_time' => $this->calculateAverageResolutionTime($lineConfig)
                ];
            }

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error getting statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate average first response time for a line
     */
    private function calculateAverageResponseTime(WhatsappLineConfig $lineConfig): float
    {
        if (!Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'line_config_id')) {
            return 0;
        }
        
        $result = $lineConfig->tickets()
            ->whereNotNull('first_response_at')
            ->whereNotNull('opened_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, first_response_at)) as avg_response_time')
            ->first();

        return round($result->avg_response_time ?? 0, 2);
    }

    /**
     * Calculate average resolution time for a line
     */
    private function calculateAverageResolutionTime(WhatsappLineConfig $lineConfig): float
    {
        if (!Schema::connection('tenant')->hasColumn('whatsapp_tickets', 'line_config_id')) {
            return 0;
        }
        
        $result = $lineConfig->tickets()
            ->whereNotNull('closed_at')
            ->whereNotNull('opened_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, closed_at)) as avg_resolution_time')
            ->first();

        return round($result->avg_resolution_time ?? 0, 2);
    }
}

