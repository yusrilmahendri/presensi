# Solusi Error 403 Forbidden dan Menu Filament Tidak Muncul

## Masalah yang Terjadi

1. **403 Forbidden saat login** - User tidak bisa mengakses panel Filament
2. **Menu Filament tidak muncul** - Hanya dashboard yang terlihat, menu resources tidak ada

## Penyebab Masalah

### 1. Role User Bukan Admin
Di file `app/Models/User.php`, method `canAccessPanel()` hanya mengizinkan user dengan role `admin`:

```php
public function canAccessPanel(\Filament\Panel $panel): bool
{
    return $this->role === 'admin';
}
```

**Jika user yang login bukan admin, akan mendapat error 403 Forbidden.**

### 2. Resources Tidak Memiliki Authorization Check
Semua Filament Resources (UserResource, AttendanceResource, dll) tidak memiliki method `shouldRegisterNavigation()`, sehingga menu tidak muncul dengan benar.

## Solusi yang Sudah Diterapkan

### ✅ 1. Menambahkan `shouldRegisterNavigation()` ke Semua Resources

Menambahkan method ini ke 5 Resources:
- `UserResource.php`
- `AttendanceResource.php`
- `ShiftResource.php`
- `LeaveResource.php`
- `AttendanceLocationResource.php`

```php
public static function shouldRegisterNavigation(): bool
{
    return auth()->check() && auth()->user()->isAdmin();
}
```

### ✅ 2. Menambahkan Logging di User Model

Menambahkan logging untuk debugging di production:

```php
public function canAccessPanel(\Filament\Panel $panel): bool
{
    $hasAccess = $this->role === 'admin';
    
    // Log untuk debugging di production
    \Log::info('User Panel Access Check', [
        'user_id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'role' => $this->role,
        'has_access' => $hasAccess,
        'panel_id' => $panel->getId(),
    ]);
    
    return $hasAccess;
}
```

## Cara Memperbaiki di Production

### Langkah 1: Periksa User di Database

Pastikan user yang login memiliki role `admin`:

```bash
php artisan tinker
```

```php
// Cek user berdasarkan email
$user = \App\Models\User::where('email', 'admin@presensi.com')->first();
echo "Role: " . $user->role;

// Atau cek semua admin
\App\Models\User::where('role', 'admin')->get(['id', 'name', 'email', 'role']);
```

### Langkah 2: Update Role User Jika Diperlukan

Jika user yang login bukan admin, update role-nya:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'EMAIL_USER_ANDA')->first();
$user->role = 'admin';
$user->save();
```

### Langkah 3: Deploy Kode Terbaru

```bash
# Pull kode terbaru dari git
git pull origin main

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize
php artisan optimize
```

### Langkah 4: Cek Log untuk Debugging

Lihat file log untuk informasi akses:

```bash
tail -f storage/logs/laravel.log
```

Cari log dengan text "User Panel Access Check" untuk melihat:
- User ID yang login
- Role user
- Apakah user memiliki akses

### Langkah 5: Buat User Admin Baru (Jika Diperlukan)

Jika tidak ada user admin, buat dengan cara:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Administrator',
    'username' => 'admin',
    'email' => 'admin@presensi.com',
    'password' => \Hash::make('password_anda'),
    'role' => 'admin',
]);
```

## Credentials Default

Berdasarkan seeder, credentials default adalah:

- **Username/Email:** admin / admin@presensi.com
- **Password:** Bismillah@1

## Verifikasi Setelah Fix

1. ✅ Login dengan user admin
2. ✅ Dashboard muncul
3. ✅ Menu sidebar muncul:
   - Absensi → Data Absensi
   - Manajemen User → Karyawan, Pengajuan Izin
   - Pengaturan → Shift, Lokasi Absen
4. ✅ Tidak ada error 403

## Troubleshooting

### Masalah: Masih 403 setelah login
**Solusi:**
- Pastikan role user adalah `admin` (bukan `karyawan`)
- Clear cache: `php artisan cache:clear`
- Cek log di `storage/logs/laravel.log`

### Masalah: Menu masih tidak muncul
**Solusi:**
- Hard refresh browser (Ctrl+Shift+R)
- Clear browser cache
- Coba logout dan login kembali

### Masalah: Lupa password admin
**Solusi:**
```bash
php artisan tinker
```
```php
$user = \App\Models\User::where('role', 'admin')->first();
$user->password = \Hash::make('password_baru');
$user->save();
```

## Catatan Penting

⚠️ **HANYA USER DENGAN ROLE `admin` YANG BISA AKSES FILAMENT PANEL**

Jika Anda ingin user `karyawan` juga bisa akses panel (tidak disarankan), ubah di:
- `app/Models/User.php` → method `canAccessPanel()`
- Semua Resources → method `shouldRegisterNavigation()`
