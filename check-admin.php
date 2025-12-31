#!/usr/bin/env php
<?php

/**
 * Script untuk cek dan update role user admin
 * Jalankan di production dengan: php check-admin.php
 */

define('LARAVEL_START', microtime(true));

// Load autoloader
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "\n";
echo "===========================================\n";
echo "   CHECK & FIX ADMIN USER - PRODUCTION    \n";
echo "===========================================\n\n";

// Cek semua user dengan role admin
echo "📋 Mencari user dengan role 'admin'...\n\n";
$admins = User::where('role', 'admin')->get();

if ($admins->count() > 0) {
    echo "✅ Ditemukan " . $admins->count() . " user admin:\n";
    echo "-------------------------------------------\n";
    foreach ($admins as $admin) {
        echo "ID: {$admin->id}\n";
        echo "Nama: {$admin->name}\n";
        echo "Email: {$admin->email}\n";
        echo "Username: " . ($admin->username ?? '-') . "\n";
        echo "Role: {$admin->role}\n";
        echo "-------------------------------------------\n";
    }
} else {
    echo "❌ TIDAK ADA USER ADMIN!\n\n";
    echo "Membuat user admin baru...\n";
    
    $admin = User::create([
        'name' => 'Administrator',
        'username' => 'admin',
        'email' => 'admin@presensi.com',
        'password' => \Hash::make('Bismillah@1'),
        'role' => 'admin',
    ]);
    
    echo "✅ User admin berhasil dibuat:\n";
    echo "   Email: admin@presensi.com\n";
    echo "   Username: admin\n";
    echo "   Password: Bismillah@1\n";
}

echo "\n";

// Cek user yang mungkin seharusnya admin tapi role-nya salah
echo "🔍 Mencari user yang mungkin seharusnya admin...\n\n";
$possibleAdmins = User::whereIn('email', [
    'admin@presensi.com',
    'admin@hadir.pioneersolve.id',
    'admin@pioneersolve.id',
])
->orWhere('username', 'admin')
->get();

if ($possibleAdmins->count() > 0) {
    foreach ($possibleAdmins as $user) {
        if ($user->role !== 'admin') {
            echo "⚠️  User '{$user->email}' memiliki role '{$user->role}'\n";
            echo "   Apakah ingin update ke role 'admin'? (y/n): ";
            
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            $response = trim($line);
            
            if (strtolower($response) === 'y') {
                $user->role = 'admin';
                $user->save();
                echo "   ✅ Role berhasil diupdate ke 'admin'\n\n";
            } else {
                echo "   ⏭️  Dilewati\n\n";
            }
            
            fclose($handle);
        }
    }
}

echo "\n";
echo "===========================================\n";
echo "              SUMMARY                      \n";
echo "===========================================\n";

$adminCount = User::where('role', 'admin')->count();
$karyawanCount = User::where('role', 'karyawan')->count();
$totalUsers = User::count();

echo "Total Users: {$totalUsers}\n";
echo "Admin: {$adminCount}\n";
echo "Karyawan: {$karyawanCount}\n";
echo "===========================================\n\n";

echo "✅ Selesai!\n\n";
echo "Untuk login sebagai admin:\n";
echo "URL: https://hadir.pioneersolve.id/admin/login\n";
echo "Email/Username: admin\n";
echo "Password: Bismillah@1 (atau password yang Anda set)\n\n";
