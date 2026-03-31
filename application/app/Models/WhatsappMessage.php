<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class WhatsappMessage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $connection = 'tenant';
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'tenant_id',
        'ticket_id',
        'sender_type',
        'sender_id',
        'sender_name',
        'channel',
        'body',
        'attachments',
        'message_id',
        'status',
        'read_at',
        'metadata'
    ];

    protected $casts = [
        'attachments' => 'array',
        'metadata' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the ticket that owns the message
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(WhatsappTicket::class, 'ticket_id');
    }

    /**
     * Get the sender (agent) if it's an agent message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Check if message is from client
     */
    public function isFromClient(): bool
    {
        return $this->sender_type === 'client';
    }

    /**
     * Check if message is from agent
     */
    public function isFromAgent(): bool
    {
        return $this->sender_type === 'agent';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'sent' => 'badge bg-primary',
            'delivered' => 'badge bg-info',
            'read' => 'badge bg-success',
            'failed' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get channel badge class
     */
    public function getChannelBadgeClassAttribute(): string
    {
        return match($this->channel) {
            'whatsapp' => 'badge bg-success',
            'email' => 'badge bg-info',
            default => 'badge bg-secondary'
        };
    }
}

