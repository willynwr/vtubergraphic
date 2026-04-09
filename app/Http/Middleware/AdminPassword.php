<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPassword
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('admin_password_passed') === true) {
            return $next($request);
        }

        return redirect()->route('admin.password.form');
    }
}