<?php

namespace App\Http\Controllers;

use App\User;

class PlayerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // とりあえず全選手
        $players = User::where('role', User::ROLE_PLAYER)
                        ->orderBy('name')
                        ->get();
        $players = User::all()->filter(function ($user) {
            return $user->isPlayer();
        })->sortBy('id');
        return view('players.index', compact('players'));
    }
}
