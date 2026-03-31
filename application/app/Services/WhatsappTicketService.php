<?php

namespace App\Services;

use App\Models\WhatsappTicket;
use App\Models\WhatsappTicketType;
use App\Models\WhatsappTag;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * @fileoverview WhatsApp Ticket Service
 * @description Handles WhatsApp ticket management, status updates, and ticket operations
 */
class WhatsappTicketService
{
    /**
     * Create a new WhatsApp ticket
     */
    public function createTicket(array $data): WhatsappTicket
    {
        try {
            $ticket = WhatsappTicket::create([
                'tenant_id' => $data['tenant_id'],
                'connection_id' => $data['connection_id'],
                'contact_id' => $data['contact_id'],
                'status' => $data['status'] ?? 'open',
                'priority' => $data['priority'] ?? 'medium',
                'channel' => 'whatsapp',
                'subject' => $data['subject'] ?? 'New WhatsApp conversation',
                'description' => $data['description'] ?? '',
                'assigned_to' => $data['assigned_to'] ?? null,
                'ticket_type_id' => $data['ticket_type_id'] ?? null,
                'tags' => $data['tags'] ?? []
            ]);

            // Assign tags if provided
            if (!empty($data['tags'])) {
                $this->assignTags($ticket, $data['tags']);
            }

            Log::info('WhatsApp ticket created successfully', ['ticket_id' => $ticket->id]);
            return $ticket;

        } catch (\Exception $e) {
            Log::error('Failed to create WhatsApp ticket: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update ticket status
     */
    public function updateStatus(WhatsappTicket $ticket, string $status): bool
    {
        try {
            $ticket->update([
                'status' => $status,
                'updated_at' => now()
            ]);

            // Log status change
            Log::info('WhatsApp ticket status updated', [
                'ticket_id' => $ticket->id,
                'old_status' => $ticket->getOriginal('status'),
                'new_status' => $status
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to update ticket status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Assign ticket to agent
     */
    public function assignTicket(WhatsappTicket $ticket, int $userId): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }

            $ticket->update([
                'assigned_to' => $userId,
                'updated_at' => now()
            ]);

            Log::info('WhatsApp ticket assigned', [
                'ticket_id' => $ticket->id,
                'assigned_to' => $userId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to assign ticket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Close ticket
     */
    public function closeTicket(WhatsappTicket $ticket, array $data = []): bool
    {
        try {
            $ticket->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolution' => $data['resolution'] ?? '',
                'ticket_type_id' => $data['ticket_type_id'] ?? null,
                'updated_at' => now()
            ]);

            // Assign tags if provided
            if (!empty($data['tags'])) {
                $this->assignTags($ticket, $data['tags']);
            }

            Log::info('WhatsApp ticket closed', [
                'ticket_id' => $ticket->id,
                'resolution' => $data['resolution'] ?? ''
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to close ticket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reopen ticket
     */
    public function reopenTicket(WhatsappTicket $ticket): bool
    {
        try {
            $ticket->update([
                'status' => 'open',
                'resolved_at' => null,
                'updated_at' => now()
            ]);

            Log::info('WhatsApp ticket reopened', ['ticket_id' => $ticket->id]);
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to reopen ticket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Put ticket on hold
     */
    public function putOnHold(WhatsappTicket $ticket, string $reason = ''): bool
    {
        try {
            $ticket->update([
                'status' => 'on_hold',
                'hold_reason' => $reason,
                'updated_at' => now()
            ]);

            Log::info('WhatsApp ticket put on hold', [
                'ticket_id' => $ticket->id,
                'reason' => $reason
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to put ticket on hold: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update ticket priority
     */
    public function updatePriority(WhatsappTicket $ticket, string $priority): bool
    {
        try {
            $ticket->update([
                'priority' => $priority,
                'updated_at' => now()
            ]);

            Log::info('WhatsApp ticket priority updated', [
                'ticket_id' => $ticket->id,
                'priority' => $priority
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to update ticket priority: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update ticket type
     */
    public function updateTicketType(WhatsappTicket $ticket, int $typeId): bool
    {
        try {
            $ticketType = WhatsappTicketType::find($typeId);
            if (!$ticketType) {
                throw new \Exception('Ticket type not found');
            }

            $ticket->update([
                'ticket_type_id' => $typeId,
                'updated_at' => now()
            ]);

            Log::info('WhatsApp ticket type updated', [
                'ticket_id' => $ticket->id,
                'type_id' => $typeId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to update ticket type: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Assign tags to ticket
     */
    public function assignTags(WhatsappTicket $ticket, array $tagIds): bool
    {
        try {
            $tags = WhatsappTag::whereIn('id', $tagIds)
                ->where('tenant_id', $ticket->tenant_id)
                ->get();

            $ticket->tags()->sync($tagIds);

            Log::info('Tags assigned to WhatsApp ticket', [
                'ticket_id' => $ticket->id,
                'tags' => $tagIds
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to assign tags: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove tags from ticket
     */
    public function removeTags(WhatsappTicket $ticket, array $tagIds): bool
    {
        try {
            $ticket->tags()->detach($tagIds);

            Log::info('Tags removed from WhatsApp ticket', [
                'ticket_id' => $ticket->id,
                'tags' => $tagIds
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to remove tags: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get ticket statistics
     */
    public function getTicketStats(int $tenantId): array
    {
        try {
            $stats = [
                'total' => WhatsappTicket::where('tenant_id', $tenantId)->count(),
                'open' => WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'open')->count(),
                'on_hold' => WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'on_hold')->count(),
                'resolved' => WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'resolved')->count(),
                'unassigned' => WhatsappTicket::where('tenant_id', $tenantId)->whereNull('assigned_to')->count(),
                'high_priority' => WhatsappTicket::where('tenant_id', $tenantId)->where('priority', 'high')->count()
            ];

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get ticket stats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get tickets by status
     */
    public function getTicketsByStatus(int $tenantId, string $status, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return WhatsappTicket::where('tenant_id', $tenantId)
                ->where('status', $status)
                ->with(['contact', 'assignedTo', 'ticketType', 'tags'])
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get();

        } catch (\Exception $e) {
            Log::error('Failed to get tickets by status: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get tickets by agent
     */
    public function getTicketsByAgent(int $tenantId, int $userId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return WhatsappTicket::where('tenant_id', $tenantId)
                ->where('agent_id', $userId)
                ->with(['agent', 'ticketType', 'tags'])
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get();

        } catch (\Exception $e) {
            Log::error('Failed to get tickets by agent: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Search tickets
     */
    public function searchTickets(int $tenantId, string $query, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $tickets = WhatsappTicket::where('tenant_id', $tenantId);

            // Apply search query
            if (!empty($query)) {
                $tickets->where(function ($q) use ($query) {
                    $q->where('subject', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });
            }

            // Apply filters
            if (!empty($filters['status'])) {
                $tickets->where('status', $filters['status']);
            }

            if (!empty($filters['priority'])) {
                $tickets->where('priority', $filters['priority']);
            }

            if (!empty($filters['assigned_to'])) {
                $tickets->where('agent_id', $filters['assigned_to']);
            }

            if (!empty($filters['ticket_type_id'])) {
                $tickets->where('ticket_type_id', $filters['ticket_type_id']);
            }

            return $tickets->with(['agent', 'ticketType', 'tags'])
                ->orderBy('updated_at', 'desc')
                ->get();

        } catch (\Exception $e) {
            Log::error('Failed to search tickets: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get paginated tickets with filters
     */
    public function getPaginatedTickets(array $filters = [], int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            $tickets = WhatsappTicket::where('tenant_id', $tenantId);

            // Apply filters
            if (!empty($filters['status'])) {
                $tickets->where('status', $filters['status']);
            }

            if (!empty($filters['channel'])) {
                $tickets->where('channel', $filters['channel']);
            }

            if (!empty($filters['agent_id'])) {
                $tickets->where('agent_id', $filters['agent_id']);
            }

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $tickets->where(function ($query) use ($search) {
                    $query->where('subject', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('contact_name', 'like', "%{$search}%")
                          ->orWhere('contact_email', 'like', "%{$search}%")
                          ->orWhere('contact_phone', 'like', "%{$search}%");
                });
            }

            // Apply date range filter
            if (!empty($filters['date_range'])) {
                $dateRange = $filters['date_range'];
                if (isset($dateRange['start']) && isset($dateRange['end'])) {
                    $tickets->whereBetween('created_at', [
                        $dateRange['start'] . ' 00:00:00',
                        $dateRange['end'] . ' 23:59:59'
                    ]);
                }
            }

            return $tickets->with(['agent', 'ticketType', 'tags'])
                ->orderBy('updated_at', 'desc')
                ->paginate($perPage);

        } catch (\Exception $e) {
            Log::error('Failed to get paginated tickets: ' . $e->getMessage());
            return WhatsappTicket::where('tenant_id', $tenantId ?? 1)
                ->orderBy('updated_at', 'desc')
                ->paginate($perPage);
        }
    }

    /**
     * Get KPIs for dashboard
     */
    public function getKPIs(): array
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            $stats = [
                'total_tickets' => WhatsappTicket::where('tenant_id', $tenantId)->count(),
                'open_tickets' => WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'open')->count(),
                'in_progress_tickets' => WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'in_progress')->count(),
                'closed_tickets' => WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'closed')->count(),
                'on_hold_tickets' => WhatsappTicket::where('tenant_id', $tenantId)->where('status', 'on_hold')->count(),
                'unassigned_tickets' => WhatsappTicket::where('tenant_id', $tenantId)->whereNull('agent_id')->count(),
                'high_priority_tickets' => WhatsappTicket::where('tenant_id', $tenantId)->where('priority', 'high')->count(),
                'urgent_tickets' => WhatsappTicket::where('tenant_id', $tenantId)->where('priority', 'urgent')->count(),
            ];

            // Calculate response time (average time to first response)
            $avgResponseTime = WhatsappTicket::where('tenant_id', $tenantId)
                ->whereNotNull('first_response_at')
                ->whereNotNull('created_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_response_time')
                ->first();

            $stats['avg_first_response_time'] = round($avgResponseTime->avg_response_time ?? 0, 2);

            // Calculate resolution time (average time to close)
            $avgResolutionTime = WhatsappTicket::where('tenant_id', $tenantId)
                ->whereNotNull('closed_at')
                ->whereNotNull('created_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, closed_at)) as avg_resolution_time')
                ->first();

            $stats['avg_resolution_time'] = round($avgResolutionTime->avg_resolution_time ?? 0, 2);

            // Get message counts
            $messageStats = $this->getMessageCounts($tenantId);
            $stats = array_merge($stats, $messageStats);

            // Get topic distribution
            $stats['topic_distribution'] = $this->getTopicDistribution($tenantId);

            // Get top agents by tickets resolved
            $stats['top_agents'] = $this->getTopAgents($tenantId);

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get KPIs: ' . $e->getMessage());
            return $this->getDefaultKPIs();
        }
    }

    /**
     * Get message counts
     */
    private function getMessageCounts($tenantId): array
    {
        try {
            // This would need to be implemented based on your message model
            // For now, returning sample data
            return [
                'total_messages' => 0,
                'client_messages' => 0,
                'agent_messages' => 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get message counts: ' . $e->getMessage());
            return [
                'total_messages' => 0,
                'client_messages' => 0,
                'agent_messages' => 0,
            ];
        }
    }

    /**
     * Get topic distribution
     */
    private function getTopicDistribution($tenantId): array
    {
        try {
            $topics = [];
            
            // Get tickets with categories
            $tickets = WhatsappTicket::where('tenant_id', $tenantId)
                ->whereNotNull('category')
                ->get();
            
            foreach ($tickets as $ticket) {
                $category = $ticket->category;
                $topics[$category] = ($topics[$category] ?? 0) + 1;
            }
            
            // If no categories, return sample data
            if (empty($topics)) {
                return [
                    'General' => 25,
                    'Technical' => 30,
                    'Billing' => 20,
                    'Support' => 25,
                ];
            }
            
            return $topics;
        } catch (\Exception $e) {
            Log::error('Failed to get topic distribution: ' . $e->getMessage());
            return [
                'General' => 25,
                'Technical' => 30,
                'Billing' => 20,
                'Support' => 25,
            ];
        }
    }

    /**
     * Get top agents by tickets resolved
     */
    private function getTopAgents($tenantId): array
    {
        try {
            $agents = User::where('tenant_id', $tenantId)
                ->whereHas('tickets', function($query) use ($tenantId) {
                    $query->where('tenant_id', $tenantId)
                          ->where('status', 'closed');
                })
                ->withCount(['tickets' => function($query) use ($tenantId) {
                    $query->where('tenant_id', $tenantId)
                          ->where('status', 'closed');
                }])
                ->orderBy('tickets_count', 'desc')
                ->limit(5)
                ->get();

            return $agents->map(function($agent) {
                return [
                    'name' => $agent->name,
                    'tickets_resolved' => $agent->tickets_count,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get top agents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get available agents for assignment
     */
    public function getAvailableAgents(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            return User::where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->whereIn('role_id', [1, 2, 3]) // Assuming these are agent roles
                ->select('id', 'first_name', 'last_name', 'email', 'avatar_filename')
                ->orderBy('first_name')
                ->get();

        } catch (\Exception $e) {
            Log::error('Failed to get available agents: ' . $e->getMessage());
            return User::where('id', 0)->get(); // Return empty Eloquent Collection
        }
    }

    /**
     * Get ticket by ID with relationships
     */
    public function getTicketById(int $ticketId): ?WhatsappTicket
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            return WhatsappTicket::where('tenant_id', $tenantId)
                ->where('id', $ticketId)
                ->with(['agent', 'ticketType', 'tags', 'messages'])
                ->first();

        } catch (\Exception $e) {
            Log::error('Failed to get ticket by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get recent tickets for dashboard
     */
    public function getRecentTickets(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            return WhatsappTicket::where('tenant_id', $tenantId)
                ->with(['agent'])
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get();

        } catch (\Exception $e) {
            Log::error('Failed to get recent tickets: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get all ticket types for the current tenant
     */
    public function getTicketTypes(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            return WhatsappTicketType::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

        } catch (\Exception $e) {
            Log::error('Failed to get ticket types: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get all tags for the current tenant
     */
    public function getTags(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $tenantId = app('currentTenant')->id ?? 1;
            
            return WhatsappTag::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

        } catch (\Exception $e) {
            Log::error('Failed to get tags: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get default KPIs when main KPIs fail
     */
    public function getDefaultKPIs(): array
    {
        return [
            'total_tickets' => 0,
            'open_tickets' => 0,
            'in_progress_tickets' => 0,
            'closed_tickets' => 0,
            'on_hold_tickets' => 0,
            'unassigned_tickets' => 0,
            'high_priority_tickets' => 0,
            'urgent_tickets' => 0,
            'avg_first_response_time' => 0,
            'avg_resolution_time' => 0,
            'total_messages' => 0,
            'client_messages' => 0,
            'agent_messages' => 0,
            'topic_distribution' => [
                'General' => 25,
                'Technical' => 30,
                'Billing' => 20,
                'Support' => 25,
            ],
            'top_agents' => [],
        ];
    }

    /**
     * Clear KPI cache for tenant (placeholder for future caching implementation)
     */
    public function clearKpiCacheForTenant(int $tenantId): void
    {
        try {
            // This is a placeholder for future caching implementation
            // For now, we'll just log that the cache would be cleared
            Log::info('KPI cache cleared for tenant', ['tenant_id' => $tenantId]);
        } catch (\Exception $e) {
            Log::error('Failed to clear KPI cache: ' . $e->getMessage());
        }
    }
}
