<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAgentTestRun extends Model
{
    protected $table = 'support_agent_test_runs';

    protected $fillable = [
        'agent_id',
        'test_creatorid',
        'test_audience',
        'test_question',
        'test_answer',
        'test_sources',
        'response_status',
        'unanswered_reasons',
        'model_name',
        'model_tokens_prompt',
        'model_tokens_completion',
        'model_tokens_total',
        'error_message',
    ];

    protected $casts = [
        'test_sources' => 'array',
        'unanswered_reasons' => 'array',
    ];

    public function agent()
    {
        return $this->belongsTo(SupportAgent::class, 'agent_id', 'id');
    }
}
