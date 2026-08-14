<?php

namespace App\Http\Controllers;

use App\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDetailController extends Controller
{
    /**
     * ログイン必須にする
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * プロフィール詳細・編集画面を表示
     */
    public function show()
    {
        $user = Auth::user();

        // ログインユーザーに紐づくプロフィールを取得（なければ空のインスタンスを作成）
        $userDetail = $user->userDetail ?? new UserDetail();

        return view('user_details.show', compact(
            'user',
            'userDetail'
        ));
    }

    /**
     * プロフィール編集画面
     */
    public function edit()
    {
        $user = Auth::user();

        $userDetail = $user->userDetail ?? new UserDetail();

        return view('user_details.edit', compact(
            'user',
            'userDetail'
        ));
    }

    /**
     * プロフィール情報を保存・更新
     */
    public function update(Request $request)
    {
        // 入力データのバリデーション
        $validated = $request->validate([
            'birthdate'   => 'nullable|date|before:today',
            'affiliation' => 'nullable|string|max:100',
            'team_name'   => 'nullable|string|max:100',
            'gender'      => 'nullable|in:male,female,other,private',
            'phone'       => 'nullable|string|max:30',
            'line_id'     => 'nullable|string|max:100',
        ]);

        // すでにデータがあれば更新（update）、なければ新規作成（create）を自動判別
        Auth::user()->userDetail()->updateOrCreate(
            ['user_id' => Auth::id()], // 検索条件
            $validated                 // 保存するデータ
        );

        return redirect()->route('profile.show')
            ->with('status', 'プロフィールを更新しました！');
    }
    //
}
