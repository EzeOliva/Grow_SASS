<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAgent extends Model
{
    protected $table = 'support_agents';

    protected $fillable = [
        'tenant_id',
        'agent_creatorid',
        'agent_name',
        'agent_identity_prompt',
        'agent_visibility',
        'is_active',
        'allow_client_chat',
        'allow_ticket_suggestions',
        'allow_document_sources',
        'agent_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_client_chat' => 'boolean',
        'allow_ticket_suggestions' => 'boolean',
        'allow_document_sources' => 'boolean',
        'agent_settings' => 'array',
    ];

    public function kbCategories()
    {
        return $this->belongsToMany(
            KbCategories::class,
            'support_agent_kb_categories',
            'agent_id',
            'kbcategory_id',
            'id',
            'kbcategory_id'
        )->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(SupportAgentDocument::class, 'agent_id', 'id');
    }

    public function testRuns()
    {
        return $this->hasMany(SupportAgentTestRun::class, 'agent_id', 'id');
    }

    public function unansweredQueries()
    {
        return $this->hasMany(SupportAgentUnansweredQuery::class, 'agent_id', 'id');
    }
}
