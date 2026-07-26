<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nutrition extends Model
{
    // テーブル名を明示的に指定します
    protected $table = 'nutritions';

    protected $fillable = [
        'user_id',
        'nutrition_date',
        'daily_memo',
        'feedback',
    ];

    public function details()
    {
        return $this->hasMany(NutritionDetail::class, 'nutrition_id', 'id');
    }
}
