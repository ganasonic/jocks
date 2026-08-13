<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Auth;

class PlayerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $staff = Auth::user();

        //$players = $staff->players()->orderBy('id')->get();
        //$players = User::whereRaw('(role & ?) != 0', [User::ROLE_PLAYER])->orderBy('id')->get();
        $players = User::whereRaw('role = ?', [User::ROLE_PLAYER])->orderBy('id')->get();

        return view('players.index', compact(
            'players',
            'staff'
        ));


    }

    public function edit(User $player)
    {
        $staff = Auth::user();

        $checked = $player->staffs()
                        ->pluck('users.id')
                        ->toArray();

        $staffs = User::where('role','>',0)
                    ->orderBy('name')
                    ->get()
                    ->filter(function($user){
                        return $user->isStaff();
                    });

        return view('players.edit',[
            'player'=>$player,
            'staffs'=>$staffs,
            'checked'=>$checked,
        ]);
    }

    public function update(Request $request, User $player)
    {
        $staffIds = $request->input('staffs', []);

        $player->staffs()->sync($staffIds);

        return redirect()
            ->route('players.index')
            ->with('success', '担当スタッフを更新しました。');
    }

    public function show(User $player)
    {
        // 選手権限を持っていないユーザーは対象外
        if (!$player->isPlayer()) {
            abort(404);
        }

        $staff = Auth::user();

        // 管理者以外は担当選手のみ
        if (!$staff->isAdmin()) {
            $isAssigned = $staff->players()
                ->where('users.id', $player->id)
                ->exists();

            if (!$isAssigned) {
                abort(403, 'この選手を操作する権限がありません。');
            }
        }

        // 対象選手モードに切り替える
        session([
            'target_player_id' => $player->id,
            'target_player_name' => $player->name,
        ]);

        return view('players.show', [
            'player' => $player,
            'staff'  => $staff,
        ]);
    }
}
