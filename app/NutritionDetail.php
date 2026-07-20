<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NutritionDetail extends Model
{
    protected $table = 'nutrition_details';

    protected $fillable = [
        'nutrition_id',
        'meal_type',
        'photo_path',
        'calories',
        'protein',
        'meal_memo',
        'meal_coach_comment',
    ];

    public function nutrition()
    {
        return $this->belongsTo(Nutrition::class, 'nutrition_id', 'id');
    }
}
