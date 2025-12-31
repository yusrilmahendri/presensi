<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class FilamentAuthenticate extends BaseAuthenticate
{
    /**
     * Redirect to the login page instead of using Filament's default login
     */
    protected function redirectTo($request): ?string
    {
        return route('karyawan.login');
    }
}
