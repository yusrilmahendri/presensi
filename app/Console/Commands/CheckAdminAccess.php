<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CheckAdminAccess extends Command
{
    protected $signature = 'admin:check {--fix : Automatically fix admin access issues}';
    
    protected $description = 'Check and fix admin user access to Filament panel';

    public function handle()
    {
        $this->info('');
        $this->info('===========================================');
        $this->info('   CHECK ADMIN ACCESS - FILAMENT PANEL    ');
        $this->info('===========================================');
        $this->info('');

        // Check for admin users
        $this->info('📋 Checking for admin users...');
        $admins = User::where('role', 'admin')->get();

        if ($admins->count() > 0) {
            $this->info("✅ Found {$admins->count()} admin user(s):");
            $this->info('');
            
            $headers = ['ID', 'Name', 'Email', 'Username', 'Role', 'Can Access Panel'];
            $rows = [];
            
            foreach ($admins as $admin) {
                $canAccess = $admin->role === 'admin' ? '✅ Yes' : '❌ No';
                $rows[] = [
                    $admin->id,
                    $admin->name,
                    $admin->email,
                    $admin->username ?? '-',
                    $admin->role,
                    $canAccess,
                ];
            }
            
            $this->table($headers, $rows);
        } else {
            $this->warn('❌ NO ADMIN USERS FOUND!');
            
            if ($this->option('fix') || $this->confirm('Do you want to create a default admin user?', true)) {
                $this->createDefaultAdmin();
            }
        }

        $this->info('');

        // Check for users that should be admin
        $this->info('🔍 Checking for users that might need admin role...');
        $possibleAdmins = User::where(function($query) {
            $query->whereIn('email', [
                'admin@presensi.com',
                'admin@hadir.pioneersolve.id',
                'admin@pioneersolve.id',
            ])
            ->orWhere('username', 'admin');
        })
        ->where('role', '!=', 'admin')
        ->get();

        if ($possibleAdmins->count() > 0) {
            $this->warn("⚠️  Found {$possibleAdmins->count()} user(s) that might need admin role:");
            
            foreach ($possibleAdmins as $user) {
                $this->line("   - {$user->email} (current role: {$user->role})");
                
                if ($this->option('fix') || $this->confirm("Update {$user->email} to admin role?", true)) {
                    $user->role = 'admin';
                    $user->save();
                    $this->info("   ✅ Updated {$user->email} to admin role");
                }
            }
        } else {
            $this->info('✅ No users found that need role update');
        }

        $this->info('');
        $this->showSummary();
        $this->info('');
        
        return 0;
    }

    protected function createDefaultAdmin()
    {
        $this->info('');
        $this->info('Creating default admin user...');

        $name = $this->ask('Admin name', 'Administrator');
        $email = $this->ask('Admin email', 'admin@presensi.com');
        $username = $this->ask('Admin username', 'admin');
        $password = $this->secret('Admin password (leave empty for default: Bismillah@1)') ?: 'Bismillah@1';

        try {
            $admin = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
            ]);

            $this->info('');
            $this->info('✅ Admin user created successfully!');
            $this->info('');
            $this->line("   Name: {$admin->name}");
            $this->line("   Email: {$admin->email}");
            $this->line("   Username: {$admin->username}");
            $this->line("   Password: {$password}");
            $this->info('');
        } catch (\Exception $e) {
            $this->error('❌ Failed to create admin user: ' . $e->getMessage());
        }
    }

    protected function showSummary()
    {
        $this->info('===========================================');
        $this->info('              SUMMARY                      ');
        $this->info('===========================================');

        $adminCount = User::where('role', 'admin')->count();
        $karyawanCount = User::where('role', 'karyawan')->count();
        $totalUsers = User::count();

        $this->line("Total Users: {$totalUsers}");
        $this->line("Admin: {$adminCount}");
        $this->line("Karyawan: {$karyawanCount}");
        
        $this->info('===========================================');
        $this->info('');
        $this->line('🔗 Admin Panel URL: https://hadir.pioneersolve.id/admin');
        $this->line('📧 Default Email: admin@presensi.com');
        $this->line('👤 Default Username: admin');
        $this->line('🔑 Default Password: Bismillah@1');
    }
}
