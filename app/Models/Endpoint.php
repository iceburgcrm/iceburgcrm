<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    use HasFactory;

    protected $table = 'ice_endpoints';

    protected $casts = [
        'status' => 'boolean',
    ];
}
