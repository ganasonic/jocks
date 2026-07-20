<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'user_id',
        'training_date',
        'title',
        'start_time',
        'end_time', 'memo'
    ];

    // 子テーブル（詳細）とのリレーション
    public function details()
    {
        return $this->hasMany(TrainingDetail::class);
    }
}
