<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    //practice_date を日付オブジェクト (Carbon) として扱う
    protected $casts = [
        'practice_date' => 'date',
    ];

    protected $fillable = [
        'user_id',
        'practice_date',
        'title',
        'practice_type',
        'target',
        'overall_coach_comment',
    ];

    public function details()
    {
        return $this->hasMany('App\PracticeDetail');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
