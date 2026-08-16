<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 管理者チェック
     */
    private function checkAdmin()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'この操作を行う権限がありません。');
        }
    }

    /**
     * ユーザー一覧
     */
    public function index()
    {
        $this->checkAdmin();

        $users = User::orderBy('id', 'asc')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * 権限編集画面
     */
    public function edit(User $user)
    {
        $this->checkAdmin();

        $roles = User::roles();
        $memberTypes = User::memberTypes();

        return view('admin.users.edit', compact(
            'user',
            'roles',
            'memberTypes'
        ));
    }

    /**
     * 権限更新
     */
    public function update(Request $request, User $user)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'role' => [
                'required',
                'integer',
                Rule::in(array_keys(User::roles())),
            ],

            'member_type' => [
                'required',
                'integer',
                Rule::in(array_keys(User::memberTypes())),
            ],
        ]);

        $user->update([
            'role'        => $validated['role'],
            'member_type' => $validated['member_type'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', $user->name . ' の権限情報を更新しました。');
    }
}
