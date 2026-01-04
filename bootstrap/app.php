<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle Token Mismatch Exception (Session Expired - Error 419)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // Jika request AJAX/API
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.',
                    'redirect' => route('login')
                ], 419);
            }

            // Jika request biasa, redirect ke login dengan pesan
            return redirect()
                ->route('login')
                ->with('error', 'Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.');
        });
    })->create();
