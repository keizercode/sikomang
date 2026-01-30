<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with([
                'message' => 'Silakan login terlebih dahulu',
                'type' => 'error'
            ]);
        }

        // Check if user is admin (group_id = 1 or 2)
        if (!in_array(session('group_id'), [1, 2])) {
            Auth::logout();
            return redirect()->route('admin.login')->with([
                'message' => 'Anda tidak memiliki akses ke halaman ini',
                'type' => 'error'
            ]);
        }

        return $next($request);
    }
}
