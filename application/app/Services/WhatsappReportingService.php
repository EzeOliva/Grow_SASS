<?php

namespace App\Services;

use App\Models\WhatsappTicket;
use App\Models\WhatsappMessage;
use App\Models\WhatsappContact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * @fileoverview WhatsApp Reporting Service - Handles all reporting and analytics
 * @description Centralized service for generating reports, KPIs, and analytics data
 */
class WhatsappReportingService
{
    /**
     * @description Get comprehensive KPIs for dashboard
     */
    public function getDashboardKPIs(): array
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            return [
                'tickets' => $this->getTicketKPIs($tenantId),
                'messages' => $this->getMessageKPIs($tenantId),
                'contacts' => $this->getContactKPIs($tenantId),
                'performance' => $this->getPerformanceKPIs($tenantId),
                'trends' => $this->getTrendKPIs($tenantId)
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard KPIs: ' . $e->getMessage());
            return $this->getDefaultKPIs();
        }
    }

    /**
     * @description Get ticket-related KPIs
     */
    private function getTicketKPIs(int $tenantId): array
    {
        try {
            $totalTickets = WhatsappTicket::where('tenant_id', $tenantId)->count();
            $openTickets = WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'open')->count();
            $inProgressTickets = WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'in_progress')->count();
            $closedTickets = WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'closed')->count();
            
            return [
                'total' => $totalTickets,
                'open' => $openTickets,
                'in_progress' => $inProgressTickets,
                'closed' => $closedTickets,
                'resolution_rate' => $totalTickets > 0 ? round(($closedTickets / $totalTickets) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'open' => 0, 'in_progress' => 0, 'closed' => 0, 'resolution_rate' => 0];
        }
    }

    /**
     * @description Get message-related KPIs
     */
    private function getMessageKPIs(int $tenantId): array
    {
        try {
            $totalMessages = WhatsappMessage::where('tenant_id', $tenantId)->count();
            $sentMessages = WhatsappMessage::where('tenant_id', $tenantId)->where('sender_type', 'agent')->count();
            $receivedMessages = WhatsappMessage::where('tenant_id', $tenantId)->where('sender_type', 'customer')->count();
            
            return [
                'total' => $totalMessages,
                'sent' => $sentMessages,
                'received' => $receivedMessages,
                'response_rate' => $receivedMessages > 0 ? round(($sentMessages / $receivedMessages) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'sent' => 0, 'received' => 0, 'response_rate' => 0];
        }
    }

    /**
     * @description Get contact-related KPIs
     */
    private function getContactKPIs(int $tenantId): array
    {
        try {
            $totalContacts = WhatsappContact::where('tenant_id', $tenantId)->count();
            $activeContacts = WhatsappContact::where('tenant_id', $tenantId)->where('last_contact_at', '>=', now()->subDays(30))->count();
            $newContacts = WhatsappContact::where('tenant_id', $tenantId)->where('created_at', '>=', now()->subDays(7))->count();
            
            return [
                'total' => $totalContacts,
                'active' => $activeContacts,
                'new_this_week' => $newContacts,
                'engagement_rate' => $totalContacts > 0 ? round(($activeContacts / $totalContacts) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'active' => 0, 'new_this_week' => 0, 'engagement_rate' => 0];
        }
    }

    /**
     * @description Get performance KPIs
     */
    private function getPerformanceKPIs(int $tenantId): array
    {
        try {
            $avgResponseTime = $this->calculateAverageResponseTime($tenantId);
            $avgResolutionTime = $this->calculateAverageResolutionTime($tenantId);
            $satisfactionScore = $this->calculateSatisfactionScore($tenantId);
            
            return [
                'avg_response_time_minutes' => $avgResponseTime,
                'avg_resolution_time_hours' => $avgResolutionTime,
                'satisfaction_score' => $satisfactionScore,
                'performance_grade' => $this->getPerformanceGrade($avgResponseTime, $avgResolutionTime, $satisfactionScore)
            ];
        } catch (\Exception $e) {
            return [
                'avg_response_time_minutes' => 0,
                'avg_resolution_time_hours' => 0,
                'satisfaction_score' => 0,
                'performance_grade' => 'N/A'
            ];
        }
    }

    /**
     * @description Get trend KPIs
     */
    private function getTrendKPIs(int $tenantId): array
    {
        try {
            $lastWeek = now()->subWeek();
            $thisWeek = now();
            
            $lastWeekTickets = WhatsappTicket::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$lastWeek->startOfWeek(), $lastWeek->endOfWeek()])
                ->count();
            
            $thisWeekTickets = WhatsappTicket::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$thisWeek->startOfWeek(), $thisWeek->endOfWeek()])
                ->count();
            
            $growth = $lastWeekTickets > 0 ? round((($thisWeekTickets - $lastWeekTickets) / $lastWeekTickets) * 100, 2) : 0;
            
            return [
                'tickets_this_week' => $thisWeekTickets,
                'tickets_last_week' => $lastWeekTickets,
                'growth_percentage' => $growth,
                'trend' => $growth > 0 ? 'increasing' : ($growth < 0 ? 'decreasing' : 'stable')
            ];
        } catch (\Exception $e) {
            return [
                'tickets_this_week' => 0,
                'tickets_last_week' => 0,
                'growth_percentage' => 0,
                'trend' => 'stable'
            ];
        }
    }

    /**
     * @description Calculate average response time
     */
    private function calculateAverageResponseTime(int $tenantId): float
    {
        try {
            $avgTime = WhatsappTicket::where('tenant_id', $tenantId)
                ->whereNotNull('first_response_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_time')
                ->first();
            
            return round($avgTime->avg_time ?? 0, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @description Calculate average resolution time
     */
    private function calculateAverageResolutionTime(int $tenantId): float
    {
        try {
            $avgTime = WhatsappTicket::where('tenant_id', $tenantId)
                ->whereNotNull('closed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_time')
                ->first();
            
            return round($avgTime->avg_time ?? 0, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @description Calculate satisfaction score
     */
    private function calculateSatisfactionScore(int $tenantId): float
    {
        try {
            // This would calculate based on actual feedback/ratings
            // For now, return a placeholder score
            return 4.2;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @description Get performance grade based on metrics
     */
    private function getPerformanceGrade(float $responseTime, float $resolutionTime, float $satisfaction): string
    {
        $score = 0;
        
        // Response time scoring (lower is better)
        if ($responseTime <= 5) $score += 30;
        elseif ($responseTime <= 15) $score += 20;
        elseif ($responseTime <= 30) $score += 10;
        
        // Resolution time scoring (lower is better)
        if ($resolutionTime <= 2) $score += 30;
        elseif ($resolutionTime <= 8) $score += 20;
        elseif ($resolutionTime <= 24) $score += 10;
        
        // Satisfaction scoring (higher is better)
        if ($satisfaction >= 4.5) $score += 40;
        elseif ($satisfaction >= 4.0) $score += 30;
        elseif ($satisfaction >= 3.5) $score += 20;
        
        if ($score >= 90) return 'A+';
        elseif ($score >= 80) return 'A';
        elseif ($score >= 70) return 'B';
        elseif ($score >= 60) return 'C';
        else return 'D';
    }

    /**
     * @description Get detailed report data
     */
    public function getDetailedReport(string $reportType, array $filters = []): array
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            switch ($reportType) {
                case 'ticket_volume':
                    return $this->getTicketVolumeReport($tenantId, $filters);
                case 'response_times':
                    return $this->getResponseTimeReport($tenantId, $filters);
                case 'agent_performance':
                    return $this->getAgentPerformanceReport($tenantId, $filters);
                case 'contact_engagement':
                    return $this->getContactEngagementReport($tenantId, $filters);
                default:
                    return ['error' => 'Invalid report type'];
            }
        } catch (\Exception $e) {
            Log::error('Error generating detailed report: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @description Get ticket volume report
     */
    private function getTicketVolumeReport(int $tenantId, array $filters): array
    {
        $period = $filters['period'] ?? 'month';
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        
        $tickets = WhatsappTicket::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'report_type' => 'ticket_volume',
            'period' => $period,
            'data' => $tickets,
            'summary' => [
                'total_tickets' => $tickets->sum('count'),
                'avg_daily_tickets' => round($tickets->avg('count'), 2),
                'peak_day' => $tickets->sortByDesc('count')->first()
            ]
        ];
    }

    /**
     * @description Get response time report
     */
    private function getResponseTimeReport(int $tenantId, array $filters): array
    {
        $tickets = WhatsappTicket::where('tenant_id', $tenantId)
            ->whereNotNull('first_response_at')
            ->selectRaw('
                TIMESTAMPDIFF(MINUTE, created_at, first_response_at) as response_time,
                status,
                priority
            ')
            ->get();
        
        return [
            'report_type' => 'response_times',
            'data' => $tickets,
            'summary' => [
                'avg_response_time' => round($tickets->avg('response_time'), 2),
                'min_response_time' => $tickets->min('response_time'),
                'max_response_time' => $tickets->max('response_time'),
                'by_priority' => $tickets->groupBy('priority')->map(function($group) {
                    return round($group->avg('response_time'), 2);
                })
            ]
        ];
    }

    /**
     * @description Get agent performance report
     */
    private function getAgentPerformanceReport(int $tenantId, array $filters): array
    {
        $agents = DB::table('whatsapp_tickets as wt')
            ->join('users as u', 'wt.agent_id', '=', 'u.id')
            ->where('wt.tenant_id', $tenantId)
            ->selectRaw('
                u.id,
                u.first_name,
                u.last_name,
                COUNT(wt.id) as total_tickets,
                AVG(TIMESTAMPDIFF(MINUTE, wt.created_at, wt.first_response_at)) as avg_response_time,
                SUM(CASE WHEN wt.status = "closed" THEN 1 ELSE 0 END) as resolved_tickets
            ')
            ->groupBy('u.id', 'u.first_name', 'u.last_name')
            ->get();
        
        return [
            'report_type' => 'agent_performance',
            'data' => $agents,
            'summary' => [
                'total_agents' => $agents->count(),
                'top_performer' => $agents->sortByDesc('resolved_tickets')->first(),
                'avg_tickets_per_agent' => round($agents->avg('total_tickets'), 2)
            ]
        ];
    }

    /**
     * @description Get contact engagement report
     */
    private function getContactEngagementReport(int $tenantId, array $filters): array
    {
        $contacts = WhatsappContact::where('tenant_id', $tenantId)
            ->withCount(['tickets', 'messages'])
            ->orderBy('tickets_count', 'desc')
            ->limit(50)
            ->get();
        
        return [
            'report_type' => 'contact_engagement',
            'data' => $contacts,
            'summary' => [
                'total_contacts' => $contacts->count(),
                'most_engaged' => $contacts->first(),
                'avg_tickets_per_contact' => round($contacts->avg('tickets_count'), 2)
            ]
        ];
    }

    /**
     * @description Export report data
     */
    public function exportReport(string $reportType, array $filters = [], string $format = 'csv'): string
    {
        try {
            $data = $this->getDetailedReport($reportType, $filters);
            
            if (isset($data['error'])) {
                throw new \Exception($data['error']);
            }
            
            switch ($format) {
                case 'csv':
                    return $this->exportToCSV($data);
                case 'json':
                    return json_encode($data, JSON_PRETTY_PRINT);
                default:
                    throw new \Exception('Unsupported export format');
            }
        } catch (\Exception $e) {
            Log::error('Error exporting report: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @description Export data to CSV format
     */
    private function exportToCSV(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Add headers
        if (isset($data['data']) && count($data['data']) > 0) {
            fputcsv($output, array_keys((array) $data['data'][0]));
            
            // Add data rows
            foreach ($data['data'] as $row) {
                fputcsv($output, (array) $row);
            }
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * @description Get default KPIs when errors occur
     */
    private function getDefaultKPIs(): array
    {
        return [
            'tickets' => ['total' => 0, 'open' => 0, 'in_progress' => 0, 'closed' => 0, 'resolution_rate' => 0],
            'messages' => ['total' => 0, 'sent' => 0, 'received' => 0, 'response_rate' => 0],
            'contacts' => ['total' => 0, 'active' => 0, 'new_this_week' => 0, 'engagement_rate' => 0],
            'performance' => [
                'avg_response_time_minutes' => 0,
                'avg_resolution_time_hours' => 0,
                'satisfaction_score' => 0,
                'performance_grade' => 'N/A'
            ],
            'trends' => [
                'tickets_this_week' => 0,
                'tickets_last_week' => 0,
                'growth_percentage' => 0,
                'trend' => 'stable'
            ]
        ];
    }
}
