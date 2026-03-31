<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientStageHistory extends Model {

    protected $table = 'client_stage_histories';
    protected $primaryKey = 'client_stage_history_id';

    protected $fillable = [
        'client_id',
        'from_stage_id',
        'to_stage_id',
        'change_detail',
        'changed_by',
        'changed_at',
    ];
}
