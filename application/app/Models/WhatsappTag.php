<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\BelongsToTenant;

/**
 * @fileoverview WhatsApp Tag Model
 * @description Manages global tags for contacts and tickets with color coding and categorization
 */
class WhatsappTag extends Model
{
    use HasFactory, BelongsToTenant;

    protected $connection = 'tenant';
    protected $table = 'whatsapp_tags';

    protected $fillable = [
        'tenant_id',
        'name',
        'color',
        'description',
        'type',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the tenant that owns the tag
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the user who created the tag
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all tickets with this tag
     */
    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(WhatsappTicket::class, 'whatsapp_ticket_tags', 'tag_id', 'ticket_id');
    }

    /**
     * Get type badge class
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'contact' => 'badge bg-primary',
            'ticket' => 'badge bg-info',
            'global' => 'badge bg-success',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get type text
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'contact' => 'Contact Tag',
            'ticket' => 'Ticket Tag',
            'global' => 'Global Tag',
            default => 'Unknown'
        };
    }

    /**
     * Get tag display HTML
     */
    public function getTagDisplayAttribute(): string
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
     * Scope for active tags
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for tags by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for global tags
     */
    public function scopeGlobal($query)
    {
        return $query->where('type', 'global');
    }

    /**
     * Scope for contact tags
     */
    public function scopeContact($query)
    {
        return $query->where('type', 'contact');
    }

    /**
     * Scope for ticket tags
     */
    public function scopeTicket($query)
    {
        return $query->where('type', 'ticket');
    }

    /**
     * Check if tag is global
     */
    public function isGlobal(): bool
    {
        return $this->type === 'global';
    }

    /**
     * Check if tag is for contacts
     */
    public function isForContacts(): bool
    {
        return in_array($this->type, ['contact', 'global']);
    }

    /**
     * Check if tag is for tickets
     */
    public function isForTickets(): bool
    {
        return in_array($this->type, ['ticket', 'global']);
    }
}

