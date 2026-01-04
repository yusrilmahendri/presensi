<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\TokenMismatchException;

class HandleSessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $exception) {
            // Handle untuk AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.',
                    'redirect' => route('login')
                ], 419);
            }

            // Handle untuk request biasa
            return redirect()
                ->route('login')
                ->with('error', 'Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.');
        }
    }
}
