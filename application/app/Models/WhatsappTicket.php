<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use App\Models\Concerns\BelongsToTenant;

class WhatsappTicket extends Model
{
    use HasFactory, BelongsToTenant;

    protected $connection = 'tenant';
    protected $table = 'whatsapp_tickets';

    protected $fillable = [
        'tenant_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'agent_id',
        'status',
        'channel',
        'subject',
        'tags',
        'opened_at',
        'first_response_at',
        'closed_at',
        'internal_notes',
        'whatsapp_number',
        'priority',
        'category',
        'ticket_type_id',
        'line_config_id',
        'last_activity_at',
        'auto_close_scheduled_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'opened_at' => 'datetime',
        'first_response_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'auto_close_scheduled_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the ticket
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the assigned agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get all messages for this ticket
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'ticket_id');
    }

    /**
     * Get the latest message
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'ticket_id')->latest();
    }

    /**
     * Get all tick list items for this ticket
     */
    public function tickLists(): HasMany
    {
        return $this->hasMany(WhatsappTickList::class, 'ticket_id');
    }

    /**
     * Get pending tick list items for this ticket
     */
    public function pendingTickLists(): HasMany
    {
        return $this->hasMany(WhatsappTickList::class, 'ticket_id')->where('status', 'pending');
    }

    /**
     * Get the ticket type
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(WhatsappTicketType::class, 'ticket_type_id');
    }

    /**
     * Get the line configuration
     */
    public function lineConfig(): BelongsTo
    {
        return $this->belongsTo(WhatsappLineConfig::class, 'line_config_id');
    }

    /**
     * Get all tags for this ticket
     */
    public function ticketTags(): BelongsToMany
    {
        return $this->belongsToMany(WhatsappTag::class, 'whatsapp_ticket_tags', 'ticket_id', 'tag_id');
    }

    /**
     * Get all tags for this ticket (alias for ticketTags)
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(WhatsappTag::class, 'whatsapp_ticket_tags', 'ticket_id', 'tag_id');
    }

    /**
     * Get completed tick list items for this ticket
     */
    public function completedTickLists(): HasMany
    {
        return $this->hasMany(WhatsappTickList::class, 'ticket_id')->where('status', 'completed');
    }

    /**
     * Calculate first response time in minutes
     */
    public function getFirstResponseTimeAttribute(): ?int
    {
        if (!$this->first_response_at || !$this->opened_at) {
            return null;
        }
        
        return $this->opened_at->diffInMinutes($this->first_response_at);
    }

    /**
     * Calculate resolution time in minutes
     */
    public function getResolutionTimeAttribute(): ?int
    {
        if (!$this->closed_at || !$this->opened_at) {
            return null;
        }
        
        return $this->opened_at->diffInMinutes($this->closed_at);
    }

    /**
     * Check if ticket is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'closed') {
            return false;
        }

        // Consider overdue if open for more than 24 hours
        $overdueThreshold = now()->subHours(24);
        return $this->opened_at && $this->opened_at->lt($overdueThreshold);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'open' => 'badge bg-warning',
            'on_hold' => 'badge bg-secondary',
            'in_progress' => 'badge bg-info',
            'closed' => 'badge bg-success',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'badge bg-secondary',
            'medium' => 'badge bg-info',
            'high' => 'badge bg-warning',
            'urgent' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for in progress tickets
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope for tickets by channel
     */
    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope for tickets by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope for overdue tickets
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'closed')
                    ->where('opened_at', '<', now()->subHours(24));
    }
}
