<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubpanelField extends Model
{
    use HasFactory;

    protected $table = 'ice_subpanel_fields';

    public function field()
    {
        return $this->hasOne(Field::class, 'id', 'field_id')->with('module');
    }
}
