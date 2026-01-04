# 🕐 Quick Guide: Session Expired Handling

## Untuk User

### ❓ Kenapa Saya Keluar dari Sistem?

Anda akan otomatis keluar (logout) jika:
- ✅ Tidak ada aktivitas selama **120 menit** (2 jam)
- ✅ Browser ditutup (jika tidak centang "Ingat Saya")
- ✅ Cookie/cache browser dihapus

### 💡 Pesan yang Akan Muncul

```
⚠️ Perhatian! 
Anda tidak aktivitas sehingga keluar dari sistem. 
Silakan login kembali.
```

### 🔧 Apa yang Harus Dilakukan?

1. **Jangan Panik** - Data yang sudah disimpan tetap aman ✅
2. **Login Kembali** - Gunakan username/password Anda
3. **Lanjutkan Pekerjaan** - Semua data tersimpan masih ada

### 💾 Tips Mencegah Kehilangan Data

| Situasi | Solusi |
|---------|--------|
| Mengisi form panjang | Simpan draft berkala (jika ada fitur save) |
| Import data besar | Gunakan fitur "Ingat Saya" saat login |
| Rapat/istirahat | Save/submit data sebelum meninggalkan komputer |
| Bekerja lama | Refresh halaman setiap 1-2 jam untuk keep session |

### ⏰ Session Warning (Jika Diaktifkan)

Jika fitur warning aktif, Anda akan melihat:
```
Sesi Anda akan berakhir dalam 5 menit karena tidak ada aktivitas. 
Klik OK untuk melanjutkan sesi.
```

**Action**: Klik **OK** untuk perpanjang session

---

## Untuk Admin

### 🔐 Konfigurasi Session Timeout

#### 1. Edit Session Lifetime
File: `.env`
```env
# Default: 120 menit (2 jam)
SESSION_LIFETIME=120

# Contoh lain:
# SESSION_LIFETIME=480  # 8 jam untuk admin
# SESSION_LIFETIME=60   # 1 jam untuk keamanan tinggi
```

#### 2. Restart Services
```bash
php artisan config:clear
php artisan cache:clear
```

### 📊 Recommended Session Settings

| User Type | Lifetime | Reason |
|-----------|----------|--------|
| Karyawan | 120 min | Balance security & UX |
| Admin | 480 min | Long working sessions |
| Super Admin | 240 min | Enhanced security |

### 🎛️ Features Configuration

#### Enable Session Warning
Tambahkan di layout (sebelum `</body>`):
```html
<script src="{{ asset('js/session-handler.js') }}"></script>
```

#### Disable Session Warning
Comment bagian ini di `public/js/session-handler.js`:
```javascript
// Auto logout warning (optional - warning sebelum session expired)
// (function() {
//   ... kode warning ...
// })();
```

#### Custom Warning Time
Edit `public/js/session-handler.js`:
```javascript
const SESSION_TIMEOUT = 120 * 60 * 1000; // sync dengan .env
const WARNING_TIME = 5 * 60 * 1000;      // 5 menit sebelum
```

### 🧪 Testing Checklist

- [ ] Test form submission setelah session expired
- [ ] Test AJAX request setelah session expired  
- [ ] Test di Chrome, Firefox, Safari
- [ ] Test dengan "Remember Me" enabled
- [ ] Test session warning popup (jika enabled)

### 🐛 Common Issues & Solutions

#### Issue 1: User Masih Lihat Error 419
```bash
# Clear all cache
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart server
php artisan serve
```

#### Issue 2: Session Expired Terlalu Cepat
```env
# Check & increase di .env
SESSION_LIFETIME=240  # increase to 4 jam

# Check session driver
SESSION_DRIVER=file  # atau database, redis
```

#### Issue 3: Redirect Loop
- Check route `login` tidak memiliki auth middleware
- Check `app/Http/Kernel.php` middleware configuration

### 📈 Monitoring

#### Log Session Expired Events
Edit `app/Exceptions/Handler.php`:
```php
if ($exception instanceof TokenMismatchException) {
    \Log::warning('Session expired', [
        'user' => auth()->id(),
        'ip' => $request->ip(),
        'url' => $request->url()
    ]);
    
    return redirect()->route('login')...
}
```

#### Track Statistics
```bash
# Count session expired today
grep "Session expired" storage/logs/laravel.log | grep $(date +%Y-%m-%d) | wc -l
```

### 🔒 Security Best Practices

1. **Regular Session**: 120 min ✅
2. **Remember Me**: Up to 30 days (configurable)
3. **HTTPS Only**: Enable in production
4. **HttpOnly Cookies**: Enabled by default
5. **Secure Cookies**: Enable for production

### 📝 User Communication Template

**Email to Users:**
```
Subject: Update - Session Timeout Sistem Presensi

Dear Team,

Untuk keamanan data, sistem presensi memiliki batas waktu inaktivitas:
- Otomatis logout setelah 2 jam tidak ada aktivitas
- Pesan akan ditampilkan saat logout otomatis
- Data yang sudah disimpan tetap aman

Tips:
✅ Gunakan fitur "Ingat Saya" jika tidak ingin login berulang
✅ Simpan data berkala saat mengisi form panjang
✅ Refresh halaman setiap 1-2 jam untuk perpanjang session

Terima kasih,
IT Team
```

### 🎯 Production Deployment

#### Checklist:
- [ ] Set `SESSION_LIFETIME` sesuai kebutuhan
- [ ] Enable HTTPS
- [ ] Set `SESSION_SECURE_COOKIE=true` di .env production
- [ ] Test di production environment
- [ ] Update user documentation
- [ ] Train support team

#### Production .env:
```env
SESSION_DRIVER=redis  # atau database untuk scale
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

---

## 📞 Support

Jika ada masalah:
1. Check dokumentasi lengkap di `SESSION_EXPIRED_HANDLING.md`
2. Test dengan langkah troubleshooting di atas
3. Check Laravel logs: `storage/logs/laravel.log`

**Status**: ✅ Production Ready  
**Last Updated**: January 2026
