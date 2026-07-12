<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    protected static function boot()
    {
        parent::boot();

        // ユーザー削除時に紐づく体調データを自動で削除する
        static::deleting(function ($user) {
            // ユーザー削除時に連動して削除を実行
            $user->userDetail()->delete();
            $user->dailyConditions()->delete();
        });
    }

    // リレーション定義
    /**
     * UserDetailモデルとのリレーション（1対1）
     */
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    /**
     * DailyConditionモデルとのリレーション（1対多）
     */
    public function dailyConditions()
    {
        return $this->hasMany(DailyCondition::class);
    }

    /**
     * ⬇️ ここに正しく入っているか確認（なければ追記して保存）
     */
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

}
