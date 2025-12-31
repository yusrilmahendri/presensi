# DEPLOYMENT INSTRUCTIONS - FIX 403 FORBIDDEN

## Masalah
403 Forbidden ketika login admin di https://hadir.pioneersolve.id/admin

## Root Cause
User model tidak mengimplementasi `FilamentUser` interface, sehingga method `canAccessPanel()` tidak dipanggil oleh Filament.

## Fix yang Sudah Diterapkan
1. ✅ User model sekarang implements `FilamentUser` interface
2. ✅ Menambahkan logging untuk debugging
3. ✅ Membuat command `php artisan admin:check` untuk auto-fix
4. ✅ Menambahkan `shouldRegisterNavigation()` di semua Resources
5. ✅ **Unified login page** - Admin & Karyawan menggunakan halaman login yang sama (`/login`)

---

## DEPLOY KE PRODUCTION

### Step 1: Push Code ke Repository

```bash
git add .
git commit -m "Fix: Implement FilamentUser interface to resolve 403 forbidden"
git push origin main
```

### Step 2: Deploy di Server Production

SSH ke server production, lalu:

```bash
# Masuk ke directory project
cd /path/to/hadir.pioneersolve.id

# Pull latest code
git pull origin main

# Install/update dependencies (jika ada yang baru)
composer install --optimize-autoloader --no-dev

# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize aplikasi
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 3: Cek & Fix Admin User

Jalankan command untuk cek dan fix masalah admin:

```bash
# Option 1: Auto fix semua masalah
php artisan admin:check --fix

# Option 2: Manual/interaktif
php artisan admin:check
```

Command ini akan:
- ✅ Cek apakah ada user dengan role 'admin'
- ✅ Membuat user admin baru jika tidak ada
- ✅ Update role user yang seharusnya admin
- ✅ Tampilkan summary dan credentials

### Step 4: Verifikasi

1. **Test Login Admin**
   - Buka: https://hadir.pioneersolve.id/login
   - Login dengan username: `admin` dan password: `Bismillah@1`
   - Pastikan auto-redirect ke `/admin`
   - Pastikan tidak ada error 403
   - Pastikan menu sidebar muncul lengkap

2. **Test Login Karyawan**
   - Buka: https://hadir.pioneersolve.id/login
   - Login dengan NIK/NIP karyawan
   - Pastikan auto-redirect ke `/dashboard`
   - Pastikan bisa akses fitur karyawan

3. **Cek Log**
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## CREDENTIALS DEFAULT

**Login URL (Admin & Karyawan):** https://hadir.pioneersolve.id/login

**Admin:**
- **Username:** admin
- **Email:** admin@presensi.com
- **Password:** Bismillah@1
- **Auto-redirect ke:** /admin (setelah login)

**Karyawan:**
- **NIK/NIP:** (sesuai data masing-masing)
- **Password:** Bismillah@1 (default)
- **Auto-redirect ke:** /dashboard (setelah login)

---

## TROUBLESHOOTING

### Masih 403 setelah deploy?

1. **Cek role user di database:**
   ```bash
   php artisan tinker
   ```
   ```php
   $user = \App\Models\User::where('email', 'admin@presensi.com')->first();
   var_dump($user->role); // Harus 'admin'
   ```

2. **Update manual jika perlu:**
   ```php
   $user->role = 'admin';
   $user->save();
   ```

3. **Cek log:**
   ```bash
   grep "User Panel Access Check" storage/logs/laravel.log
   ```
   
   Akan muncul log seperti:
   ```json
   {
     "user_id": 1,
     "name": "Administrator",
     "email": "admin@presensi.com",
     "role": "admin",
     "has_access": true,
     "panel_id": "admin"
   }
   ```

4. **Clear browser cache:**
   - Hard refresh: Ctrl+Shift+R (Windows) atau Cmd+Shift+R (Mac)
   - Atau buka di incognito/private window

### User admin tidak ada sama sekali?

Buat user admin baru:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Administrator',
    'username' => 'admin',
    'email' => 'admin@presensi.com',
    'password' => \Hash::make('Bismillah@1'),
    'role' => 'admin',
]);
```

### Menu masih tidak muncul?

Pastikan:
1. ✅ User sudah login sebagai admin (role = 'admin')
2. ✅ Clear cache browser
3. ✅ Logout dan login ulang
4. ✅ Cek console browser untuk error JavaScript

---

## VALIDATION CHECKLIST

Setelah deploy, pastikan:

- [ ] Code terbaru sudah di-pull di server
- [ ] Cache sudah di-clear
- [ ] Ada user dengan role 'admin' di database
- [ ] Bisa login tanpa error 403
- [ ] Menu sidebar muncul lengkap:
  - [ ] Dashboard
  - [ ] Absensi → Data Absensi
  - [ ] Manajemen User → Karyawan
  - [ ] Manajemen User → Pengajuan Izin
  - [ ] Pengaturan → Shift
  - [ ] Pengaturan → Lokasi Absen
- [ ] Log tidak ada error

---

## FILES YANG BERUBAH

1. `app/Models/User.php` - Implement FilamentUser interface
2. `app/Console/Commands/CheckAdminAccess.php` - Command baru untuk cek admin
3. `app/Filament/Resources/*.php` - Tambah shouldRegisterNavigation()
4. `app/Http/Middleware/FilamentAuthenticate.php` - **BARU** - Custom middleware untuk redirect
5. `app/Providers/Filament/AdminPanelProvider.php` - Config unified login
6. `resources/views/auth/login.blade.php` - Update teks untuk admin & karyawan
7. `check-admin.php` - Standalone script
8. `TROUBLESHOOTING.md` - Dokumentasi lengkap
9. `DEPLOYMENT.md` - File ini

---

## SUPPORT

Jika masih ada masalah:
1. Cek file `TROUBLESHOOTING.md` untuk solusi lengkap
2. Jalankan `php artisan admin:check --fix`
3. Cek log di `storage/logs/laravel.log`
