<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class WhatsappConnection extends Model
{
    use HasFactory, BelongsToTenant;

    protected $connection = 'tenant';
    protected $table = 'whatsapp_connections';

    protected $fillable = [
        'tenant_id',
        'connection_name',
        'connection_type',
        'status',
        'phone_number',
        'connection_data',
        'qr_code',
        'last_connected_at',
        'last_error_at',
        'error_message',
        'webhook_config',
        'is_active'
    ];

    protected $casts = [
        'connection_data' => 'array',
        'webhook_config' => 'array',
        'last_connected_at' => 'datetime',
        'last_error_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the connection
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'connected' => 'badge bg-success',
            'connecting' => 'badge bg-warning',
            'disconnected' => 'badge bg-secondary',
            'error' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get connection type badge class
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->connection_type) {
            'baileys' => 'badge bg-info',
            'twilio' => 'badge bg-primary',
            '360dialog' => 'badge bg-success',
            'gupshup' => 'badge bg-warning',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Check if connection is active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->attributes['is_active'] && $this->status === 'connected';
    }
}

