<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    use HasFactory;

    protected $table = 'ice_endpoints';

    public function connector() {
        return $this->belongsTo(Connector::class);
    }

    public function commands() {
        return $this->hasMany(ConnectorCommand::class);
    }
}
