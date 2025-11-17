<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUmkmAuth
{
    public function handle(Request $request, Closure $next)
    {
        // kalau belum ada user_id di session, paksa ke login
        if (!session()->has('user_id')) {
            return redirect()->route('login'); // pastikan nama route login = 'login'
        }

        return $next($request);
    }
}
