# 🚨 FIX URGENT: 403 Forbidden - Filament Admin Panel

## TL;DR - Quick Fix untuk Production

```bash
# 1. Pull code terbaru
git pull origin main

# 2. Clear cache
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# 3. Auto fix admin access
php artisan admin:check --fix

# 4. Optimize
php artisan optimize

# 5. Test login: https://hadir.pioneersolve.id/admin
```

---

## 📋 Yang Sudah Diperbaiki

### ✅ 1. User Model - Implement FilamentUser Interface

**File:** `app/Models/User.php`

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }
}
```

### ✅ 2. Resources - Authorization Check

**Files:** All Filament Resources (`app/Filament/Resources/*.php`)

```php
public static function shouldRegisterNavigation(): bool
{
    return auth()->check() && auth()->user()->isAdmin();
}
```

### ✅ 3. Artisan Command - Admin Check & Fix

**File:** `app/Console/Commands/CheckAdminAccess.php`

```bash
# Auto fix
php artisan admin:check --fix

# Manual/interactive
php artisan admin:check
```

### ✅ 4. Unified Login Page

**Admin dan Karyawan sekarang menggunakan halaman login yang sama:**

- **URL:** `/login` (untuk semua user)
- Admin akan auto-redirect ke `/admin` setelah login
- Karyawan akan auto-redirect ke `/dashboard` setelah login
- **Files:**
  - `app/Http/Middleware/FilamentAuthenticate.php` - Custom middleware
  - `app/Providers/Filament/AdminPanelProvider.php` - Config untuk redirect
  - `resources/views/auth/login.blade.php` - Unified login page

---

## 🎯 Root Cause

**Masalah:** User model tidak mengimplementasi `FilamentUser` interface

**Dampak:**
- ❌ Method `canAccessPanel()` tidak dipanggil oleh Filament
- ❌ Semua user mendapat 403 Forbidden
- ❌ Menu tidak muncul

**Solusi:** Implement `FilamentUser` interface + clear cache

---

## 📖 Dokumentasi Lengkap

- **DEPLOYMENT.md** - Instruksi deployment lengkap
- **TROUBLESHOOTING.md** - Troubleshooting & FAQ
- **check-admin.php** - Standalone script untuk cek admin

---

## 🔑 Default Credentials

**Admin:**
```
URL Login: https://hadir.pioneersolve.id/login (sama dengan karyawan)
Username: admin
Email: admin@presensi.com
Password: Bismillah@1

Setelah login, akan auto-redirect ke: /admin
```

**Karyawan:**
```
URL Login: https://hadir.pioneersolve.id/login
NIK/NIP: (sesuai data masing-masing)
Password: Bismillah@1 (default)

Setelah login, akan auto-redirect ke: /dashboard
```

---

## ✅ Validation Checklist

Setelah deploy, pastikan:

- [ ] Login berhasil tanpa 403
- [ ] Menu sidebar muncul:
  - [ ] Dashboard
  - [ ] Absensi → Data Absensi
  - [ ] Manajemen User → Karyawan & Pengajuan Izin
  - [ ] Pengaturan → Shift & Lokasi Absen
- [ ] Bisa akses semua halaman
- [ ] No errors di log

---

## 🆘 Still Having Issues?

1. **Cek log:**
   ```bash
   tail -f storage/logs/laravel.log | grep "User Panel Access"
   ```

2. **Cek role user:**
   ```bash
   php artisan tinker
   ```
   ```php
   \App\Models\User::where('email', 'admin@presensi.com')->value('role');
   // HARUS return: "admin"
   ```

3. **Manual fix role:**
   ```php
   $user = \App\Models\User::where('email', 'admin@presensi.com')->first();
   $user->role = 'admin';
   $user->save();
   ```

---

**Created:** 31 December 2025  
**Status:** ✅ Ready for deployment  
**Priority:** 🔴 URGENT
