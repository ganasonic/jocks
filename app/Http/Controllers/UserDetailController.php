<?php

namespace App\Http\Controllers;

use App\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
    public function update_org(Request $request)
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

    /**
     * プロフィール情報を保存・更新
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'birthdate'   => 'nullable|date|before:today',
            'affiliation' => 'nullable|string|max:100',
            'team_name'   => 'nullable|string|max:100',
            'gender'      => 'nullable|in:male,female,other,private',
            'phone'       => 'nullable|string|max:30',
            'line_id'     => 'nullable|string|max:100',

            // パスワード変更
            'current_password'      => 'nullable|string',
            'password'              => 'nullable|string|min:8|confirmed',
        ]);

        /*
        * ============================================================
        * プロフィール更新
        * ============================================================
        */

        $profileData = [
            'birthdate'   => $validated['birthdate'] ?? null,
            'affiliation' => $validated['affiliation'] ?? null,
            'team_name'   => $validated['team_name'] ?? null,
            'gender'      => $validated['gender'] ?? null,
            'phone'       => $validated['phone'] ?? null,
            'line_id'     => $validated['line_id'] ?? null,
        ];

        $user->userDetail()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            $profileData
        );


        /*
        * ============================================================
        * パスワード変更
        *
        * 新しいパスワードが入力された場合だけ実行
        * ============================================================
        */

        if (!empty($validated['password'])) {

            /*
            * 現在のパスワード入力必須
            */
            if (empty($validated['current_password'])) {

                throw ValidationException::withMessages([
                    'current_password' => [
                        '現在のパスワードを入力してください。',
                    ],
                ]);
            }


            /*
            * 現在のパスワードが正しいか確認
            */
            if (!Hash::check(
                $validated['current_password'],
                $user->password
            )) {

                throw ValidationException::withMessages([
                    'current_password' => [
                        '現在のパスワードが正しくありません。',
                    ],
                ]);
            }


            /*
            * 新しいパスワードを保存
            */
            $user->password = Hash::make(
                $validated['password']
            );

            $user->save();
        }


        return redirect()
            ->route('profile.show')
            ->with(
                'status',
                !empty($validated['password'])
                    ? 'プロフィールとパスワードを更新しました。'
                    : 'プロフィールを更新しました。'
            );
    }

}
