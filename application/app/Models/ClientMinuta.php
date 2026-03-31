<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMinuta extends Model {

    protected $table = 'client_minutas';
    protected $primaryKey = 'client_minuta_id';
    protected $guarded = ['client_minuta_id'];
    const CREATED_AT = 'minuta_created';
    const UPDATED_AT = 'minuta_updated';

}
