<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientCapacitacion extends Model {

    protected $table = 'client_capacitaciones';
    protected $primaryKey = 'client_capacitacion_id';
    protected $guarded = ['client_capacitacion_id'];
    const CREATED_AT = 'capacitacion_created';
    const UPDATED_AT = 'capacitacion_updated';

}
