<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientStage extends Model {

    protected $table = 'client_stages';
    protected $primaryKey = 'client_stage_id';

    protected $fillable = [
        'client_stage_title',
        'client_stage_description',
        'client_stage_position',
        'client_stage_active',
    ];
}
