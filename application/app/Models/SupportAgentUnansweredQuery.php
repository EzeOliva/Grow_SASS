<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAgentUnansweredQuery extends Model
{
    protected $table = 'support_agent_unanswered_queries';

    protected $fillable = [
        'agent_id',
        'test_run_id',
        'unanswered_creatorid',
        'unanswered_audience',
        'unanswered_question',
        'unanswered_reason',
        'unanswered_reason_details',
        'unanswered_status',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
    ];

    public function agent()
    {
        return $this->belongsTo(SupportAgent::class, 'agent_id', 'id');
    }

    public function testRun()
    {
        return $this->belongsTo(SupportAgentTestRun::class, 'test_run_id', 'id');
    }
}
