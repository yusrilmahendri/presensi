# Cara Mengaktifkan Menu Pengajuan Izin

Menu "Pengajuan Izin" sudah dibuat dan seharusnya muncul di sidebar admin panel.

## Langkah-langkah Troubleshooting:

### 1. Clear Cache
Jalankan perintah berikut di terminal:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan filament:cache-components
```

### 2. Logout dan Login Kembali
- Logout dari admin panel
- Login kembali dengan user yang memiliki role "admin"

### 3. Hard Refresh Browser
- Chrome/Edge: Tekan `Ctrl + Shift + R` (Windows) atau `Cmd + Shift + R` (Mac)
- Firefox: Tekan `Ctrl + F5` (Windows) atau `Cmd + Shift + R` (Mac)

### 4. Verifikasi User adalah Admin
Pastikan user yang login memiliki role "admin". Cek di database:
```bash
php artisan tinker
```
Kemudian jalankan:
```php
\App\Models\User::where('role', 'admin')->get(['id', 'name', 'email', 'role']);
```

### 5. Akses Langsung
Coba akses langsung URL menu:
```
http://127.0.0.1:8000/admin/leaves
```

## Lokasi Menu

Setelah berhasil, menu "Pengajuan Izin" akan muncul di:
- **Sidebar > Absensi > Pengajuan Izin**
- Berada di bawah menu "Data Absensi"

## Fitur yang Tersedia

### Di Dashboard:
1. **Widget Statistik Pengajuan Izin** - Menampilkan:
   - Total Pengajuan Izin
   - Menunggu Persetujuan
   - Disetujui
   - Ditolak
   - Bulan Ini

2. **Widget Pengajuan Pending** - Tabel pengajuan yang perlu direview dengan tombol:
   - Setujui
   - Tolak
   - Detail

3. **Widget Riwayat Approval** - 5 approval terakhir

### Di Halaman Pengajuan Izin:
1. **Tabs Filter Cepat**:
   - Semua
   - Menunggu
   - Disetujui
   - Ditolak
   - Sakit
   - Izin
   - Cuti

2. **Filter Lanjutan**:
   - Filter Status (multiple)
   - Filter Jenis (multiple)
   - Filter Karyawan (searchable)
   - Filter Tanggal Pengajuan (range)
   - Filter Periode Izin (range)

3. **Bulk Actions**:
   - Setujui Terpilih
   - Tolak Terpilih
   - Hapus Terpilih

4. **Actions per Row**:
   - Setujui (untuk status pending)
   - Tolak (untuk status pending)
   - View Detail
   - Edit (untuk status pending)
   - Delete

## Jika Masih Belum Muncul

Periksa file berikut sudah ada:
- `app/Filament/Resources/LeaveResource.php`
- `app/Filament/Resources/LeaveResource/Pages/ListLeaves.php`
- `app/Filament/Resources/LeaveResource/Pages/CreateLeave.php`
- `app/Filament/Resources/LeaveResource/Pages/EditLeave.php`
- `app/Filament/Resources/LeaveResource/Pages/ViewLeave.php`
- `app/Filament/Widgets/LeaveStats.php`
- `app/Filament/Widgets/PendingLeaves.php`
- `app/Filament/Widgets/RecentLeaveApprovals.php`

Semua file sudah dibuat dengan benar.
