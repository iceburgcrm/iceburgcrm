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
}
