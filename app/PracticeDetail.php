<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PracticeDetail extends Model
{
    protected $fillable = [
        'practice_id',
        'menu_name',
        'runs_or_time',
        'rating',
        'impression',
        'notice',
        'video_url',
        'coach_rating',
        'coach_comment',
        'item_coach_comment',
    ];

    public function practice()
    {
        return $this->belongsTo('App\Practice');
    }
}
