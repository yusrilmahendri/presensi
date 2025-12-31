<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckLeaveResourceCommand extends Command
{
    protected $signature = 'check:leave-resource';
    protected $description = 'Check if Leave Resource is properly configured';

    public function handle()
    {
        $this->info('Checking Leave Resource Configuration...');
        $this->newLine();

        // Check if files exist
        $files = [
            'app/Filament/Resources/LeaveResource.php',
            'app/Filament/Resources/LeaveResource/Pages/ListLeaves.php',
            'app/Filament/Resources/LeaveResource/Pages/CreateLeave.php',
            'app/Filament/Resources/LeaveResource/Pages/EditLeave.php',
            'app/Filament/Resources/LeaveResource/Pages/ViewLeave.php',
            'app/Filament/Widgets/LeaveStats.php',
            'app/Filament/Widgets/PendingLeaves.php',
            'app/Filament/Widgets/RecentLeaveApprovals.php',
        ];

        foreach ($files as $file) {
            $fullPath = base_path($file);
            if (File::exists($fullPath)) {
                $this->info("✓ {$file}");
            } else {
                $this->error("✗ {$file} - NOT FOUND");
            }
        }

        $this->newLine();

        // Check Leave model
        if (class_exists(\App\Models\Leave::class)) {
            $this->info('✓ Leave Model exists');
        } else {
            $this->error('✗ Leave Model NOT FOUND');
        }

        // Check if LeaveResource is discoverable
        if (class_exists(\App\Filament\Resources\LeaveResource::class)) {
            $this->info('✓ LeaveResource class can be loaded');
            
            $resource = \App\Filament\Resources\LeaveResource::class;
            $this->info("  - Model: " . $resource::getModel());
            $this->info("  - Navigation Label: " . $resource::getNavigationLabel());
            $this->info("  - Navigation Group: " . $resource::getNavigationGroup());
            $this->info("  - Navigation Sort: " . $resource::getNavigationSort());
        } else {
            $this->error('✗ LeaveResource class cannot be loaded');
        }

        $this->newLine();

        // Check admin users
        $adminCount = \App\Models\User::where('role', 'admin')->count();
        if ($adminCount > 0) {
            $this->info("✓ Found {$adminCount} admin user(s)");
            
            $admins = \App\Models\User::where('role', 'admin')->get(['id', 'name', 'email']);
            foreach ($admins as $admin) {
                $this->line("  - {$admin->name} ({$admin->email})");
            }
        } else {
            $this->warn('⚠ No admin users found. Create an admin user first.');
        }

        $this->newLine();

        // Check if routes exist
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $leaveRoutes = collect($routes)->filter(function ($route) {
            return str_contains($route->getName() ?? '', 'filament.admin.resources.leaves');
        });

        if ($leaveRoutes->count() > 0) {
            $this->info("✓ Found {$leaveRoutes->count()} Leave resource routes");
        } else {
            $this->error('✗ No Leave resource routes found');
        }

        $this->newLine();
        $this->info('Recommendation:');
        $this->line('1. Clear cache: php artisan cache:clear');
        $this->line('2. Clear config: php artisan config:clear');
        $this->line('3. Logout and login again as admin');
        $this->line('4. Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)');
        $this->line('5. Try accessing directly: http://127.0.0.1:8000/admin/leaves');

        return Command::SUCCESS;
    }
}
