<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    const ROLE_PLAYER        = 1;   // 0001
    const ROLE_NUTRITIONIST  = 2;   // 0010
    const ROLE_TRAINER       = 4;   // 0100
    const ROLE_COACH         = 8;   // 1000
    const ROLE_ADMIN         = 15;  // 1111

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role',
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

    // =========================================
    // リレーション
    // =========================================

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

    /**
     * このスタッフが担当している選手
     */
    public function players()
    {
        return $this->belongsToMany(
            User::class,
            'player_staff',
            'staff_id',
            'player_id'
        );
    }

    /**
     * この選手を担当しているスタッフ
     */
    public function staffs()
    {
        return $this->belongsToMany(
            User::class,
            'player_staff',
            'player_id',
            'staff_id'
        );
    }

    // =========================================
    // 権限判定
    // =========================================

    /**
     * 選手
     */
    public function isPlayer()
    {
        return ($this->role & self::ROLE_PLAYER) === self::ROLE_PLAYER;
    }

    /**
     * 管理栄養士
     */
    public function isNutritionist()
    {
        return ($this->role & self::ROLE_NUTRITIONIST) === self::ROLE_NUTRITIONIST;
    }

    /**
     * トレーナー
     */
    public function isTrainer()
    {
        return ($this->role & self::ROLE_TRAINER) === self::ROLE_TRAINER;
    }

    /**
     * コーチ
     */
    public function isCoach()
    {
        return ($this->role & self::ROLE_COACH) === self::ROLE_COACH;
    }

    /**
     * 管理者
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * スタッフ判定
     */
    public function isStaff()
    {
        return $this->isCoach()
            || $this->isTrainer()
            || $this->isNutritionist()
            || $this->isAdmin();
    }

    public function hasRole($role)
    {
        return ($this->role & $role) === $role;
    }

    // =========================================
    // 表示用
    // =========================================

    /**
     * 権限名
     */
    public function getRoleNameAttribute()
    {
        if ($this->isAdmin()) {
            return '管理者';
        }

        if ($this->isCoach()) {
            return 'コーチ';
        }

        if ($this->isTrainer()) {
            return 'トレーナー';
        }

        if ($this->isNutritionist()) {
            return '管理栄養士';
        }

        return '選手';
    }

    /**
     * 権限一覧
     */
    public static function roles()
    {
        return [
            self::ROLE_PLAYER       => '選手',
            self::ROLE_NUTRITIONIST => '管理栄養士',
            self::ROLE_TRAINER      => 'トレーナー',
            self::ROLE_COACH        => 'コーチ',
            self::ROLE_ADMIN        => '管理者',
        ];
    }

    /**
     * 現在操作対象のユーザーIDを取得
     *
     * 対象選手が選択されている場合はその選手ID。
     * 選択されていない場合はログイン本人のID。
     */
    public function targetPlayerId()
    {
        if (session()->has('target_player_id')) {
            return session('target_player_id');
        }

        return $this->id;
    }

    /**
     * 現在操作対象の選手を取得
     */
    public function targetPlayer()
    {
        $playerId = $this->targetPlayerId();

        if (!$playerId) {
            return null;
        }

        return self::find($playerId);
    }

    public function hasTargetPlayer()
    {
        return session()->has('target_player_id');
    }
}
