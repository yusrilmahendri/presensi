<?php

namespace App\Filament\Resources\UserResource\Pages\Reports;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;

class MonthlyRecap extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.reports.monthly-recap';
}
