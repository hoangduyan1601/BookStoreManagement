<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $vaiTro = strtolower(trim(Auth::user()->VaiTro ?? ''));
        if ($vaiTro !== 'quanly' && $vaiTro !== 'admin' && $vaiTro !== 'nhanvien') {
            return redirect('/');
        }

        return $next($request);
    }
}
