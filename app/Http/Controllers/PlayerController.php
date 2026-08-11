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
        $players = User::whereRaw('(role & ?) != 0', [User::ROLE_PLAYER])
            ->orderBy('id')
            ->get();

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
}
