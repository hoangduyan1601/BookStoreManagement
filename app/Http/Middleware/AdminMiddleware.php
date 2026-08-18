<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $vaiTro = strtolower(trim(Auth::user()->VaiTro ?? ''));
        if ($vaiTro !== 'quanly' && $vaiTro !== 'admin' && $vaiTro !== 'nhanvien') {
            return redirect('/');
        }

        return $next($request);
    }
}
