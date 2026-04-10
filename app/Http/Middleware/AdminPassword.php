<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPassword
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('admin_password_passed') === true) {
            if (!$request->session()->has('admin_scope')) {
                $request->session()->put('admin_scope', 'general');
            }

            return $next($request);
        }

        return redirect()->route('admin.password.form');
    }
}