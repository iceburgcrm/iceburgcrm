<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationshipModule extends Model
{
    use HasFactory;

    protected $table = 'ice_relationship_modules';

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }

    public function modulefields()
    {
        return $this->belongsTo(Module::class)->with('fields');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
