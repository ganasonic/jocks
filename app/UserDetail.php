<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    // 一括保存を許可するカラムを指定
    protected $fillable = [
        'user_id',
        'birthdate',
        'affiliation',
    ];

    // Userモデルとのリレーション（逆方向：詳細は1つのユーザーに属する）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
