<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappTicket;
use App\Models\WhatsappMessage;
use App\Models\User;
use App\Models\WhatsappTag;
use App\Models\WhatsappTicketType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @fileoverview WhatsApp Reporting Controller
 * @description Handles advanced reporting and analytics including date-range filters, 
 * message counts, topic analysis, and agent performance metrics
 */
class WhatsappReportingController extends Controller
{
    /**
     * Display the main reporting dashboard
     */
    public function index(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $filters = $this->getFilters($request);
        
        $reportData = $this->generateReportData($dateRange, $filters);
        
        return view('whatsapp.reporting.index', compact('reportData', 'dateRange', 'filters'));
    }

    /**
     * Generate comprehensive report data
     */
    private function generateReportData(array $dateRange, array $filters): array
    {
        $query = WhatsappTicket::query()
            ->where('tenant_id', app('currentTenant')->id ?? 1) // Get from current tenant context
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        // Apply filters
        if (!empty($filters['status'])) {
            // Handle both single values and arrays
            if (is_array($filters['status'])) {
                $query->whereIn('status', $filters['status']);
            } else {
                $query->where('status', $filters['status']);
            }
        }
        
        if (!empty($filters['channel'])) {
            // Handle both single values and arrays
            if (is_array($filters['channel'])) {
                $query->whereIn('channel', $filters['channel']);
            } else {
                $query->where('channel', $filters['channel']);
            }
        }
        
        if (!empty($filters['agent_id'])) {
            // Handle both single values and arrays
            if (is_array($filters['agent_id'])) {
                $query->whereIn('agent_id', $filters['agent_id']);
            } else {
                $query->where('agent_id', $filters['agent_id']);
            }
        }

        $tickets = $query->get();

        return [
            'ticket_metrics' => $this->getTicketMetrics($tickets),
            'message_metrics' => $this->getMessageMetrics($tickets, $dateRange),
            'response_time_metrics' => $this->getResponseTimeMetrics($tickets),
            'topic_analysis' => $this->getTopicAnalysis($tickets),
            'agent_performance' => $this->getAgentPerformance($tickets, $dateRange),
            'channel_breakdown' => $this->getChannelBreakdown($tickets),
            'priority_distribution' => $this->getPriorityDistribution($tickets),
            'hourly_distribution' => $this->getHourlyDistribution($tickets),
            'tag_usage' => $this->getTagUsage($tickets),
            'ticket_type_breakdown' => $this->getTicketTypeBreakdown($tickets)
        ];
    }

    /**
     * Get ticket count metrics
     */
    private function getTicketMetrics($tickets): array
    {
        $total = $tickets->count();
        
        return [
            'total' => $total,
            'open' => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'on_hold' => $tickets->where('status', 'on_hold')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
            'new_today' => $tickets->where('created_at', '>=', Carbon::today())->count(),
            'resolved_today' => $tickets->where('status', 'closed')
                ->where('closed_at', '>=', Carbon::today())->count()
        ];
    }

    /**
     * Get message count metrics
     */
    private function getMessageMetrics($tickets, array $dateRange): array
    {
        $ticketIds = $tickets->pluck('id');
        
        $totalMessages = WhatsappMessage::whereIn('ticket_id', $ticketIds)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $clientMessages = WhatsappMessage::whereIn('ticket_id', $ticketIds)
            ->where('sender_type', 'client')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $agentMessages = WhatsappMessage::whereIn('ticket_id', $ticketIds)
            ->where('sender_type', 'agent')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $systemMessages = WhatsappMessage::whereIn('ticket_id', $ticketIds)
            ->where('sender_type', 'system')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        return [
            'total' => $totalMessages,
            'client' => $clientMessages,
            'agent' => $agentMessages,
            'system' => $systemMessages,
            'avg_per_ticket' => $totalMessages > 0 ? round($totalMessages / $tickets->count(), 2) : 0
        ];
    }

    /**
     * Get response time metrics
     */
    private function getResponseTimeMetrics($tickets): array
    {
        $ticketsWithResponse = $tickets->whereNotNull('first_response_at');
        
        if ($ticketsWithResponse->isEmpty()) {
            return [
                'avg_first_response' => 0,
                'avg_resolution' => 0,
                'response_time_distribution' => [],
                'resolution_time_distribution' => []
            ];
        }

        $firstResponseTimes = $ticketsWithResponse->map(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->first_response_at);
        });

        $resolutionTimes = $tickets->whereNotNull('closed_at')->map(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->closed_at);
        });

        return [
            'avg_first_response' => round($firstResponseTimes->avg(), 2),
            'avg_resolution' => $resolutionTimes->isNotEmpty() ? round($resolutionTimes->avg(), 2) : 0,
            'response_time_distribution' => $this->getTimeDistribution($firstResponseTimes),
            'resolution_time_distribution' => $this->getTimeDistribution($resolutionTimes)
        ];
    }

    /**
     * Get topic analysis (based on tags and categories)
     */
    private function getTopicAnalysis($tickets): array
    {
        $topics = [];
        
        // Analyze by tags
        foreach ($tickets as $ticket) {
            if ($ticket->tags && is_array($ticket->tags)) {
                foreach ($ticket->tags as $tag) {
                    $topics[$tag] = ($topics[$tag] ?? 0) + 1;
                }
            }
            
            if ($ticket->category) {
                $topics[$ticket->category] = ($topics[$ticket->category] ?? 0) + 1;
            }
        }

        arsort($topics);
        
        return [
            'top_topics' => array_slice($topics, 0, 10, true),
            'total_topics' => count($topics),
            'topic_distribution' => $topics
        ];
    }

    /**
     * Get agent performance metrics
     */
    private function getAgentPerformance($tickets, array $dateRange): array
    {
        $agents = User::where('type', 'team')
            ->get();
        $performance = [];

        foreach ($agents as $agent) {
            $agentTickets = $tickets->where('agent_id', $agent->id);
            $totalTickets = $agentTickets->count();
            
            if ($totalTickets === 0) {
                continue;
            }

            $resolvedTickets = $agentTickets->where('status', 'closed')->count();
            $avgResponseTime = $this->calculateAgentAvgResponseTime($agent->id, $dateRange);
            $avgResolutionTime = $this->calculateAgentAvgResolutionTime($agent->id, $dateRange);

            $performance[] = [
                'agent_id' => $agent->id,
                'agent_name' => $agent->first_name . ' ' . $agent->last_name,
                'total_tickets' => $totalTickets,
                'resolved_tickets' => $resolvedTickets,
                'resolution_rate' => round(($resolvedTickets / $totalTickets) * 100, 2),
                'avg_response_time' => $avgResponseTime,
                'avg_resolution_time' => $avgResolutionTime,
                'current_workload' => $agentTickets->whereIn('status', ['open', 'in_progress'])->count()
            ];
        }

        // Sort by resolved tickets (descending)
        usort($performance, function ($a, $b) {
            return $b['resolved_tickets'] <=> $a['resolved_tickets'];
        });

        return $performance;
    }

    /**
     * Get channel breakdown
     */
    private function getChannelBreakdown($tickets): array
    {
        $channels = $tickets->groupBy('channel');
        
        $breakdown = [];
        foreach ($channels as $channel => $channelTickets) {
            $breakdown[$channel] = [
                'count' => $channelTickets->count(),
                'percentage' => round(($channelTickets->count() / $tickets->count()) * 100, 2),
                'avg_response_time' => $this->calculateChannelAvgResponseTime($channelTickets),
                'avg_resolution_time' => $this->calculateChannelAvgResolutionTime($channelTickets)
            ];
        }

        return $breakdown;
    }

    /**
     * Get priority distribution
     */
    private function getPriorityDistribution($tickets): array
    {
        $priorities = $tickets->groupBy('priority');
        
        $distribution = [];
        foreach ($priorities as $priority => $priorityTickets) {
            $distribution[$priority] = [
                'count' => $priorityTickets->count(),
                'percentage' => round(($priorityTickets->count() / $tickets->count()) * 100, 2)
            ];
        }

        return $distribution;
    }

    /**
     * Get hourly distribution
     */
    private function getHourlyDistribution($tickets): array
    {
        $hourly = array_fill(0, 24, 0);
        
        foreach ($tickets as $ticket) {
            $hour = (int) $ticket->created_at->format('G');
            $hourly[$hour]++;
        }

        return $hourly;
    }

    /**
     * Get tag usage statistics
     */
    private function getTagUsage($tickets): array
    {
        $tagUsage = [];
        
        foreach ($tickets as $ticket) {
            if ($ticket->tags && is_array($ticket->tags)) {
                foreach ($ticket->tags as $tag) {
                    $tagUsage[$tag] = ($tagUsage[$tag] ?? 0) + 1;
                }
            }
        }

        arsort($tagUsage);
        
        return [
            'most_used' => array_slice($tagUsage, 0, 10, true),
            'total_unique_tags' => count($tagUsage),
            'tag_distribution' => $tagUsage
        ];
    }

    /**
     * Get ticket type breakdown
     */
    private function getTicketTypeBreakdown($tickets): array
    {
        $types = $tickets->groupBy('ticket_type_id');
        
        $breakdown = [];
        foreach ($types as $typeId => $typeTickets) {
            $type = WhatsappTicketType::find($typeId);
            if ($type) {
                $breakdown[$type->name] = [
                    'count' => $typeTickets->count(),
                    'percentage' => round(($typeTickets->count() / $tickets->count()) * 100, 2),
                    'avg_resolution_time' => $this->calculateTypeAvgResolutionTime($typeTickets)
                ];
            }
        }

        arsort($breakdown);
        return $breakdown;
    }

    /**
     * Calculate agent average response time
     */
    private function calculateAgentAvgResponseTime(int $agentId, array $dateRange): float
    {
        $tickets = WhatsappTicket::where('agent_id', $agentId)
            ->whereNotNull('first_response_at')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get();

        if ($tickets->isEmpty()) {
            return 0;
        }

        $totalMinutes = $tickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->first_response_at);
        });

        return round($totalMinutes / $tickets->count(), 2);
    }

    /**
     * Calculate agent average resolution time
     */
    private function calculateAgentAvgResolutionTime(int $agentId, array $dateRange): float
    {
        $tickets = WhatsappTicket::where('agent_id', $agentId)
            ->whereNotNull('closed_at')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get();

        if ($tickets->isEmpty()) {
            return 0;
        }

        $totalMinutes = $tickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->closed_at);
        });

        return round($totalMinutes / $tickets->count(), 2);
    }

    /**
     * Calculate channel average response time
     */
    private function calculateChannelAvgResponseTime($tickets): float
    {
        $ticketsWithResponse = $tickets->whereNotNull('first_response_at');
        
        if ($ticketsWithResponse->isEmpty()) {
            return 0;
        }

        $totalMinutes = $ticketsWithResponse->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->first_response_at);
        });

        return round($totalMinutes / $ticketsWithResponse->count(), 2);
    }

    /**
     * Calculate channel average resolution time
     */
    private function calculateChannelAvgResolutionTime($tickets): float
    {
        $closedTickets = $tickets->whereNotNull('closed_at');
        
        if ($closedTickets->isEmpty()) {
            return 0;
        }

        $totalMinutes = $closedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->closed_at);
        });

        return round($totalMinutes / $closedTickets->count(), 2);
    }

    /**
     * Calculate type average resolution time
     */
    private function calculateTypeAvgResolutionTime($tickets): float
    {
        $closedTickets = $tickets->whereNotNull('closed_at');
        
        if ($closedTickets->isEmpty()) {
            return 0;
        }

        $totalMinutes = $closedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->closed_at);
        });

        return round($totalMinutes / $closedTickets->count(), 2);
    }

    /**
     * Get time distribution for charts
     */
    private function getTimeDistribution($times): array
    {
        $distribution = [
            '0-15min' => 0,
            '15-30min' => 0,
            '30-60min' => 0,
            '1-2h' => 0,
            '2-4h' => 0,
            '4-8h' => 0,
            '8-24h' => 0,
            '24h+' => 0
        ];

        foreach ($times as $time) {
            if ($time <= 15) {
                $distribution['0-15min']++;
            } elseif ($time <= 30) {
                $distribution['15-30min']++;
            } elseif ($time <= 60) {
                $distribution['30-60min']++;
            } elseif ($time <= 120) {
                $distribution['1-2h']++;
            } elseif ($time <= 240) {
                $distribution['2-4h']++;
            } elseif ($time <= 480) {
                $distribution['4-8h']++;
            } elseif ($time <= 1440) {
                $distribution['8-24h']++;
            } else {
                $distribution['24h+']++;
            }
        }

        return $distribution;
    }

    /**
     * Get date range from request
     */
    private function getDateRange(Request $request): array
    {
        $start = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        return [
            'start' => Carbon::parse($start)->startOfDay(),
            'end' => Carbon::parse($end)->endOfDay(),
            'start_formatted' => $start,
            'end_formatted' => $end
        ];
    }

    /**
     * Get filters from request
     */
    private function getFilters(Request $request): array
    {
        return [
            'status' => $request->get('status'),
            'channel' => $request->get('channel'),
            'agent_id' => $request->get('agent_id'),
            'priority' => $request->get('priority'),
            'ticket_type' => $request->get('ticket_type')
        ];
    }

    /**
     * Export report data
     */
    public function export(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $filters = $this->getFilters($request);
        $reportData = $this->generateReportData($dateRange, $filters);

        // TODO: Implement export functionality (CSV, Excel, PDF)
        return response()->json([
            'success' => true,
            'message' => 'Export functionality coming soon',
            'data' => $reportData
        ]);
    }

    /**
     * Get real-time KPIs for dashboard
     */
    public function getRealTimeKPIs()
    {
        try {
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();

            $kpis = [
                'today' => [
                                'new_tickets' => WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('created_at', '>=', $today)->count(),
            'resolved_tickets' => WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('status', 'closed')
                ->where('closed_at', '>=', $today)->count(),
                    'avg_response_time' => $this->getTodayAvgResponseTime()
                ],
                'this_week' => [
                                'new_tickets' => WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('created_at', '>=', $thisWeek)->count(),
            'resolved_tickets' => WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('status', 'closed')
                ->where('closed_at', '>=', $thisWeek)->count()
                ],
                'this_month' => [
                                'new_tickets' => WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('created_at', '>=', $thisMonth)->count(),
            'resolved_tickets' => WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
                ->where('status', 'closed')
                ->where('closed_at', '>=', $thisMonth)->count()
                ]
            ];

            return response()->json($kpis);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error getting real-time KPIs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's average response time
     */
    private function getTodayAvgResponseTime(): float
    {
        $tickets = WhatsappTicket::where('tenant_id', app('currentTenant')->id ?? 1)
            ->whereNotNull('first_response_at')
            ->where('created_at', '>=', Carbon::today())
            ->get();

        if ($tickets->isEmpty()) {
            return 0;
        }

        $totalMinutes = $tickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->first_response_at);
        });

        return round($totalMinutes / $tickets->count(), 2);
    }
}
