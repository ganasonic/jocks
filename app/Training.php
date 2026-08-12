<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'user_id',
        'training_date',
        'created_by',
        'updated_by',
        'title',
        'start_time',
        'end_time',
        'memo',
        'feedback',
    ];

    // 子テーブル（詳細）とのリレーション
    public function details()
    {
        return $this->hasMany(TrainingDetail::class);
    }
}
