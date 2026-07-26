<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * 使い方:
     * ->middleware('role:player')
     * ->middleware('role:coach,trainer')
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $map = [
            'player'       => User::ROLE_PLAYER,
            'nutritionist' => User::ROLE_NUTRITIONIST,
            'trainer'      => User::ROLE_TRAINER,
            'coach'        => User::ROLE_COACH,
            'admin'        => User::ROLE_ADMIN,
        ];

        foreach ($roles as $role) {

            if (!isset($map[$role])) {
                continue;
            }

            if ($user->hasRole($map[$role])) {
                return $next($request);
            }
        }

        abort(403, 'このページを表示する権限がありません。');
    }
}
