<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleConvertable extends Model
{
    use HasFactory;

    public function module(){
        return $this->hasMany(Module::class, 'id', 'module_id');
    }
}
