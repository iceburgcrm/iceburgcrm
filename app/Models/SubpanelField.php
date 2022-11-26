<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubpanelField extends Model
{
    use HasFactory;

    public function field()
    {
        return $this->hasOne(Field::class, 'id', 'field_id')->with('module');
    }
}
