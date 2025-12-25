<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class RBACManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.r-b-a-c-management';
    
    protected static ?string $navigationLabel = 'RBAC & Permissions';
    
    protected static ?string $navigationGroup = 'Manajemen Super Admin';
    
    protected static ?int $navigationSort = 100;
    
    protected static ?string $title = 'Role-Based Access Control';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function getAccessMatrix(): array
    {
        return [
            [
                'resource' => 'Organizations',
                'super_admin' => ['view', 'create', 'update', 'delete'],
                'admin' => [],
                'karyawan' => [],
                'description' => 'Manajemen bisnis (UMKM, Instansi, Perusahaan)',
            ],
            [
                'resource' => 'Admin Accounts',
                'super_admin' => ['view', 'create', 'update', 'delete'],
                'admin' => [],
                'karyawan' => [],
                'description' => 'Akun admin untuk setiap bisnis',
            ],
            [
                'resource' => 'Karyawan',
                'super_admin' => [],
                'admin' => ['view', 'create', 'update', 'delete'],
                'karyawan' => [],
                'description' => 'Data karyawan dalam organization',
            ],
            [
                'resource' => 'Shifts',
                'super_admin' => [],
                'admin' => ['view', 'create', 'update', 'delete'],
                'karyawan' => [],
                'description' => 'Pengaturan shift kerja',
            ],
            [
                'resource' => 'Lokasi Absen',
                'super_admin' => [],
                'admin' => ['view', 'create', 'update', 'delete'],
                'karyawan' => [],
                'description' => 'Titik lokasi untuk absensi GPS',
            ],
            [
                'resource' => 'Data Absensi',
                'super_admin' => [],
                'admin' => ['view', 'create', 'update', 'delete'],
                'karyawan' => ['view-own', 'create-own'],
                'description' => 'Record absensi check in/out',
            ],
            [
                'resource' => 'Pengajuan Izin',
                'super_admin' => [],
                'admin' => ['view', 'approve', 'reject', 'delete'],
                'karyawan' => ['view-own', 'create', 'update-own'],
                'description' => 'Cuti, sakit, izin karyawan',
            ],
        ];
    }

    public function getRegisteredPolicies(): array
    {
        return [
            'OrganizationPolicy' => [
                'model' => 'Organization',
                'methods' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
                'scope' => 'Super Admin Only',
            ],
            'UserPolicy' => [
                'model' => 'User',
                'methods' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
                'scope' => 'Organization-based',
            ],
            'ShiftPolicy' => [
                'model' => 'Shift',
                'methods' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
                'scope' => 'Organization-based',
            ],
            'AttendanceLocationPolicy' => [
                'model' => 'AttendanceLocation',
                'methods' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
                'scope' => 'Organization-based',
            ],
            'AttendancePolicy' => [
                'model' => 'Attendance',
                'methods' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
                'scope' => 'Organization-based',
            ],
            'LeavePolicy' => [
                'model' => 'Leave',
                'methods' => ['viewAny', 'view', 'create', 'update', 'delete', 'approve', 'restore', 'forceDelete'],
                'scope' => 'Organization-based',
            ],
        ];
    }

    public function getRegisteredGates(): array
    {
        return [
            'manage-organizations' => 'Kelola semua bisnis dan organizations',
            'manage-admins' => 'Kelola akun admin bisnis',
            'manage-employees' => 'Kelola karyawan dalam organization',
            'manage-attendance' => 'Kelola data absensi',
            'approve-leaves' => 'Approve/reject pengajuan izin',
        ];
    }

    public function getRoleDescriptions(): array
    {
        return [
            'super_admin' => [
                'name' => 'Super Administrator',
                'color' => 'danger',
                'icon' => 'heroicon-o-shield-check',
                'description' => 'Akses penuh untuk mengelola semua bisnis dan admin accounts. Tidak bisa akses data operasional (karyawan, absensi, izin).',
                'login_url' => '/admin/login',
                'default_redirect' => '/admin',
            ],
            'admin' => [
                'name' => 'Admin Bisnis',
                'color' => 'warning',
                'icon' => 'heroicon-o-user-circle',
                'description' => 'Kelola karyawan, shift, lokasi, absensi, dan approve izin dalam satu organization/bisnis.',
                'login_url' => '/admin/login',
                'default_redirect' => '/admin',
            ],
            'karyawan' => [
                'name' => 'Karyawan',
                'color' => 'success',
                'icon' => 'heroicon-o-user',
                'description' => 'Akses untuk check in/out absensi, submit pengajuan izin, dan lihat data pribadi.',
                'login_url' => '/login',
                'default_redirect' => '/dashboard',
            ],
        ];
    }
}
