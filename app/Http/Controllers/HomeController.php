<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
//use Config;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/');
        }
        //dd($user->role);
        // ホーム画面に来たら「本人モード」に戻す
        session()->forget([
            'target_player_id',
            'target_player_name',
        ]);

        // 全メニュー取得
        $allMenu = Config::get('menu.homemenu1');

        // 権限に応じたメニューのみ抽出
        $menu = [];

        foreach ($allMenu as $item) {

            // rolesが設定されていない場合は全員表示
            if (!isset($item['roles'])) {
                $menu[] = $item;
                continue;
            }

            // 権限チェック
            foreach ($item['roles'] as $role) {
                if ($user->hasRole($role)) {
                    $menu[] = $item;
                    break;
                }
            }
        }

        return view('home', [
            'user' => $user,
            'menu' => $menu,
        ]);
    }

}
