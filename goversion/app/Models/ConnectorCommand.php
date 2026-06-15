<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectorCommand extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'ice_connector_commands';

    protected $casts = [
        'status' => 'boolean',
    ];

    public function connector()
    {
        return $this->belongsto(Connector::class, 'connector_id', 'id');
    }


    public function endpoint() {
        return $this->belongsTo(Endpoint::class);
    }
}
