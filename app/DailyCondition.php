<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyCondition extends Model
{
    protected $table = 'conditions';

// 一括保存を許可するカラムを指定
    protected $fillable = [
        'user_id',
        'date',
        'body_temperature',
        'condition_level',
        'mood_level',
        'wakeup_time',
        'bedtime',
        'meals_memo',
        'feedback',
        'feedback_by',
    ];

    // Userモデルとのリレーション（逆方向：体調データは1つのユーザーに属する）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * フィードバックを入力したスタッフ
     */
    public function feedbackUser()
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }
}
