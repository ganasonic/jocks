<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    // ⬇️ ここで保存を許可するカラムを正確に指定します
    protected $fillable = [
        'user_id',
        'period_type',
        'start_date',
        'end_date',
        'title',
        'action_plan',
        'result_memo',
        'impression',
        'achievement_rate',
        'countermeasure',
        'next_action',
        'coach_comment',
        'coach_id'
    ];

    /**
     * この目標を所有するユーザー（逆リレーション）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
