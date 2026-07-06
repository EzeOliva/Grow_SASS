<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAgentDocument extends Model
{
    protected $table = 'support_agent_documents';

    protected $fillable = [
        'agent_id',
        'agent_document_name',
        'agent_document_original_name',
        'agent_document_mime',
        'agent_document_size',
        'agent_document_disk',
        'agent_document_path',
        'agent_document_visibility',
        'agent_document_status',
        'agent_document_extracted_text',
        'agent_document_chunks',
        'agent_document_last_indexed_at',
        'agent_document_error',
    ];

    protected $casts = [
        'agent_document_last_indexed_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(SupportAgent::class, 'agent_id', 'id');
    }
}
