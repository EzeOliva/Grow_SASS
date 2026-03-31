<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToTenant;

/**
 * @fileoverview WhatsApp Line Configuration Model
 * @description Manages per-line settings for WhatsApp connections including auto-assignment, 
 * welcome messages, closure messages, and inactivity handling
 */
class WhatsappLineConfig extends Model
{
    use HasFactory, BelongsToTenant;

    protected $connection = 'tenant';
    protected $table = 'whatsapp_line_configs';

    protected $fillable = [
        'tenant_id',
        'connection_id',
        'line_name',
        'assignment_mode',
        'auto_assign_enabled',
        'welcome_message',
        'closure_message',
        'inactivity_message',
        'inactivity_timeout_minutes',
        'auto_assign_agents',
        'routing_rules',
        'is_active'
    ];

    protected $casts = [
        'auto_assign_enabled' => 'boolean',
        'auto_assign_agents' => 'array',
        'routing_rules' => 'array',
        'is_active' => 'boolean',
        'inactivity_timeout_minutes' => 'integer'
    ];

    /**
     * Get the tenant that owns the line configuration
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the WhatsApp connection for this line
     */
    public function connection(): BelongsTo
    {
        return $this->belongsTo(WhatsappConnection::class, 'connection_id');
    }

    /**
     * Get all tickets for this line
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(WhatsappTicket::class, 'line_config_id');
    }

    /**
     * Get assignment mode badge class
     */
    public function getAssignmentModeBadgeClassAttribute(): string
    {
        return match($this->assignment_mode) {
            'manual' => 'badge bg-secondary',
            'auto_round_robin' => 'badge bg-info',
            'auto_load_balanced' => 'badge bg-success',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get assignment mode text
     */
    public function getAssignmentModeTextAttribute(): string
    {
        return match($this->assignment_mode) {
            'manual' => 'Manual Assignment',
            'auto_round_robin' => 'Auto Round-Robin',
            'auto_load_balanced' => 'Auto Load-Balanced',
            default => 'Unknown'
        };
    }

    /**
     * Check if auto-assignment is enabled
     */
    public function isAutoAssignmentEnabled(): bool
    {
        return $this->auto_assign_enabled && in_array($this->assignment_mode, ['auto_round_robin', 'auto_load_balanced']);
    }

    /**
     * Get next agent for round-robin assignment
     */
    public function getNextAgentForRoundRobin(): ?User
    {
        if (!$this->auto_assign_agents || !is_array($this->auto_assign_agents)) {
            return null;
        }

        // Get the last assigned agent from this line
        $lastAssignedTicket = $this->tickets()
            ->whereNotNull('agent_id')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$lastAssignedTicket) {
            // First assignment, return first agent
            return User::find($this->auto_assign_agents[0] ?? null);
        }

        // Find next agent in round-robin order
        $currentIndex = array_search($lastAssignedTicket->agent_id, $this->auto_assign_agents);
        if ($currentIndex === false) {
            $currentIndex = -1;
        }

        $nextIndex = ($currentIndex + 1) % count($this->auto_assign_agents);
        return User::find($this->auto_assign_agents[$nextIndex] ?? null);
    }

    /**
     * Get next agent for load-balanced assignment
     */
    public function getNextAgentForLoadBalanced(): ?User
    {
        if (!$this->auto_assign_agents || !is_array($this->auto_assign_agents)) {
            return null;
        }

        // Get agent workload (open + in-progress tickets)
        $agentWorkload = $this->tickets()
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('agent_id')
            ->selectRaw('agent_id, COUNT(*) as workload')
            ->groupBy('agent_id')
            ->pluck('workload', 'agent_id')
            ->toArray();

        // Find agent with lowest workload
        $lowestWorkload = PHP_INT_MAX;
        $selectedAgentId = null;

        foreach ($this->auto_assign_agents as $agentId) {
            $workload = $agentWorkload[$agentId] ?? 0;
            if ($workload < $lowestWorkload) {
                $lowestWorkload = $workload;
                $selectedAgentId = $agentId;
            }
        }

        return User::find($selectedAgentId);
    }

    /**
     * Get next agent based on assignment mode
     */
    public function getNextAgent(): ?User
    {
        if (!$this->isAutoAssignmentEnabled()) {
            return null;
        }

        return match($this->assignment_mode) {
            'auto_round_robin' => $this->getNextAgentForRoundRobin(),
            'auto_load_balanced' => $this->getNextAgentForLoadBalanced(),
            default => null
        };
    }

    /**
     * Check if inactivity timeout is configured
     */
    public function hasInactivityTimeout(): bool
    {
        return !empty($this->inactivity_message) && $this->inactivity_timeout_minutes > 0;
    }

    /**
     * Get inactivity timeout in seconds
     */
    public function getInactivityTimeoutSeconds(): int
    {
        return $this->inactivity_timeout_minutes * 60;
    }
}

