<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connector extends Model
{
    use HasFactory;

    protected $table = 'ice_connectors';

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $guarded = [];

    public function commands()
    {
        return $this->hasMany(ConnectorCommand::class, 'connector_id', 'id');
    }

    public function endpoints() {
        return $this->hasMany(Endpoint::class);
    }


    public function connector()
    {
        return $this->belongsTo(Connector::class);
    }

    public function endpoint()
    {
        return $this->belongsTo(Endpoint::class);
    }

}

