<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMiddleware
{
    public function handle(Request $request, Closure $next, $group)
    {
        $user = Auth::guard()->user();

        if (! $user->hasGroup($group)) {
            return redirect()->route('index');
        }

        return $next($request);
    }
}
