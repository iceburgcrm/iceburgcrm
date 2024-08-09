<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class IcePersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory;
    protected $table = 'ice_personal_access_tokens';

    protected $guarded = [
    ];

    protected $casts = [
        'abilities' => 'array', // Ensure abilities are cast to an array
    ];
}
