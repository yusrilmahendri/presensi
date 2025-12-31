<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;

class RBACMatrix extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.r-b-a-c-matrix';
}
