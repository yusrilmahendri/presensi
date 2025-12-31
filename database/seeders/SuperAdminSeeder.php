<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@presensi.com',
            'username' => 'superadmin',
            'password' => Hash::make('Bismillah@1'),
            'role' => 'super_admin',
            'organization_id' => null, // Super admin tidak terikat ke organization
        ]);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Username: superadmin');
        $this->command->info('Email: superadmin@presensi.com');
        $this->command->info('Password: Bismillah@1');
    }
}
