<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

/**
 * @fileoverview WhatsApp Quick Template Model
 * @description Manages quick message templates for agents with categories, shortcuts, and usage tracking
 */
class WhatsappQuickTemplate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $connection = 'tenant';
    protected $table = 'whatsapp_quick_templates';

    protected $fillable = [
        'tenant_id',
        'name',
        'content',
        'category',
        'shortcut',
        'is_active',
        'sort_order',
        'usage_count',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'usage_count' => 'integer'
    ];

    /**
     * Get the tenant that owns the template
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the user who created the template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get category badge class
     */
    public function getCategoryBadgeClassAttribute(): string
    {
        return match($this->category) {
            'greeting' => 'badge bg-primary',
            'closing' => 'badge bg-success',
            'support' => 'badge bg-info',
            'sales' => 'badge bg-warning',
            'technical' => 'badge bg-secondary',
            'general' => 'badge bg-dark',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get category text
     */
    public function getCategoryTextAttribute(): string
    {
        return ucfirst($this->category);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Check if template is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for templates by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for ordered templates
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope for most used templates
     */
    public function scopeMostUsed($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * Get template preview (truncated content)
     */
    public function getPreviewAttribute(): string
    {
        return strlen($this->content) > 100 
            ? substr($this->content, 0, 100) . '...' 
            : $this->content;
    }

    /**
     * Get word count
     */
    public function getWordCountAttribute(): int
    {
        return str_word_count(strip_tags($this->content));
    }

    /**
     * Get character count
     */
    public function getCharacterCountAttribute(): int
    {
        return strlen(strip_tags($this->content));
    }

    /**
     * Check if template has shortcut
     */
    public function hasShortcut(): bool
    {
        return !empty($this->shortcut);
    }

    /**
     * Get shortcut display text
     */
    public function getShortcutDisplayAttribute(): string
    {
        return $this->shortcut ? "/{$this->shortcut}" : '';
    }
}

