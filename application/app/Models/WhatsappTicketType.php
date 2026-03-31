<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToTenant;

/**
 * @fileoverview WhatsApp Ticket Type Model
 * @description Manages ticket types for categorization with editable lists per company
 */
class WhatsappTicketType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $connection = 'tenant';
    protected $table = 'whatsapp_ticket_types';

    protected $fillable = [
        'tenant_id',
        'name',
        'color',
        'description',
        'is_active',
        'sort_order',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the tenant that owns the ticket type
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the user who created the ticket type
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all tickets of this type
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(WhatsappTicket::class, 'ticket_type_id');
    }

    /**
     * Get type display HTML
     */
    public function getTypeDisplayAttribute(): string
    {
        return sprintf(
            '<span class="badge" style="background-color: %s; color: %s;">%s</span>',
            $this->color,
            $this->getContrastColor($this->color),
            htmlspecialchars($this->name)
        );
    }

    /**
     * Get contrast color for text (black or white)
     */
    private function getContrastColor(string $hexColor): string
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        // Return black for light colors, white for dark colors
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    /**
     * Scope for active ticket types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered ticket types
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get ticket count for this type
     */
    public function getTicketCountAttribute(): int
    {
        return $this->tickets()->count();
    }

    /**
     * Get open ticket count for this type
     */
    public function getOpenTicketCountAttribute(): int
    {
        return $this->tickets()->whereIn('status', ['open', 'on_hold', 'in_progress'])->count();
    }

    /**
     * Get closed ticket count for this type
     */
    public function getClosedTicketCountAttribute(): int
    {
        return $this->tickets()->where('status', 'closed')->count();
    }

    /**
     * Check if type is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if type can be deleted (no tickets)
     */
    public function canBeDeleted(): bool
    {
        return $this->tickets()->count() === 0;
    }
}

