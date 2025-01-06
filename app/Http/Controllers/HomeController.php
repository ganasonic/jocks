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
    //public function index()
    //{
    //    return view('home');
    //}
    public function index()
    {
        $user = Auth::user();

        if($user==null){
            return redirect('/');
        }
        $menu = Config::get('menu.homemenu1');
        //ddd($user->role);
        //return view('home', compact('menu'));
        return view('home',
        [
            'user' => $user,
            'menu' => $menu
        ]);
    }


}
