<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleGroup extends Model
{
    use HasFactory;

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function getReviewAvgAttribute()
    {

        $reviews = $this->modules()->where('status', 1)->get();
        $total = 0;
        foreach($reviews as $review) {
            $total += $review->review_avg;
        }
        return $total / $reviews->count();
    }


}
