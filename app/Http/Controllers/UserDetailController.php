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
        // ログインユーザーに紐づくプロフィールを取得（なければ空のインスタンスを作成）
        $userDetail = Auth::user()->userDetail ?? new UserDetail();

        return view('user_details.show', compact('userDetail'));
    }

    /**
     * プロフィール情報を保存・更新
     */
    public function update(Request $request)
    {
        // 入力データのバリデーション
        $validated = $request->validate([
            'birthdate' => 'nullable|date|before:today',
            'affiliation' => 'nullable|string|max:100',
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
