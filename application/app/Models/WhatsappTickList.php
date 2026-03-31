<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\Concerns\BelongsToTenant;

class WhatsappTickList extends Model
{
    use HasFactory, BelongsToTenant;

    protected $connection = 'tenant';
    protected $table = 'whatsapp_tick_lists';

    protected $fillable = [
        'tenant_id',
        'ticket_id',
        'title',
        'description',
        'status',
        'created_by',
        'assigned_to',
        'due_date',
        'priority'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Get the tenant that owns the tick list item
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the ticket that owns the tick list item
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(WhatsappTicket::class, 'ticket_id');
    }

    /**
     * Get the user who created the tick list item
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to complete the tick list item
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get priority text
     */
    public function getPriorityTextAttribute(): string
    {
        return match($this->priority) {
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            4 => 'Urgent',
            default => 'Low'
        };
    }

    /**
     * Get priority color class
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            1 => 'success',
            2 => 'info',
            3 => 'warning',
            4 => 'danger',
            default => 'success'
        };
    }

    /**
     * Check if item is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date || $this->status === 'completed') {
            return false;
        }
        
        return $this->due_date->isPast();
    }

    /**
     * Get days until due (negative if overdue)
     */
    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->due_date) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date, false);
    }
}

