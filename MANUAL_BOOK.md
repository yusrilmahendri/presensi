# 📘 MANUAL BOOK - SISTEM PRESENSI

## Panduan Lengkap Penggunaan Sistem Presensi

**Version:** 2.0  
**Last Updated:** 01 Januari 2026  
**Support:** yusrilmahendri.yusril@gmail.com | 085161597598

---

## 📑 DAFTAR ISI

1. [Pengenalan Sistem](#pengenalan-sistem)
2. [Akses & Login](#akses--login)
3. [Role & Hak Akses](#role--hak-akses)
4. [Dashboard Admin](#dashboard-admin)
5. [Dashboard Karyawan](#dashboard-karyawan)
6. [Manajemen User](#manajemen-user)
7. [Manajemen Absensi](#manajemen-absensi)
8. [Shift & Jadwal](#shift--jadwal)
9. [Lokasi Absensi](#lokasi-absensi)
10. [Izin & Cuti](#izin--cuti)
11. [Lembur (Overtime)](#lembur-overtime)
12. [Laporan](#laporan)
13. [Notifikasi](#notifikasi)
14. [Multi-Tenancy](#multi-tenancy)
15. [FAQ](#faq)
16. [Troubleshooting](#troubleshooting)
17. [Kontak Support](#kontak-support)

---

## 1. PENGENALAN SISTEM

### Apa itu Sistem Presensi?

Sistem Presensi adalah aplikasi berbasis web untuk manajemen kehadiran karyawan dengan fitur:

### Fitur Utama Sistem

- ✅ Absensi berbasis GPS (Geofencing)
- ✅ Foto selfie saat absen
- ✅ Manajemen shift kerja
- ✅ Pengajuan izin & cuti online
- ✅ Approval lembur
- ✅ Laporan & export data (Excel/PDF)
- ✅ Notifikasi otomatis
- ✅ Multi-tenancy (Multi-perusahaan)
- ✅ Kalender absensi
- ✅ Dashboard interaktif

---

## 2. AKSES & LOGIN

### URL Akses

#### Admin Panel
```
URL: https://domain-anda.com/admin
```

#### Karyawan
```
Absensi: https://domain-anda.com/attendance
Dashboard: https://domain-anda.com/dashboard
```

### Cara Login

#### Admin
1. Buka URL: `/admin`
2. Masukkan **Username/Email** dan **Password**
3. Klik **Sign In**
4. Anda akan diarahkan ke Admin Panel

#### Karyawan
1. Buka URL: `/login`
2. Masukkan **Username** dan **Password**
3. Klik **Login**
4. Anda akan diarahkan ke Dashboard Karyawan

### Lupa Password?

1. Hubungi admin untuk reset password
2. Admin dapat mengubah password di menu **Data User**

---

## 3. ROLE & HAK AKSES

### Jenis Role dalam Sistem

Sistem Presensi memiliki 2 role utama:

#### 1. Admin
**Hak Akses:**
- Mengelola user/karyawan dalam organization
- Melihat dan mengelola data absensi
- Approval izin, cuti, dan lembur karyawan
- Mengakses semua laporan (rekap bulanan, keterlambatan, overtime)
- Mengatur shift kerja dan jadwal
- Mengelola lokasi absensi (geofencing)
- Mengelola hari libur
- Mengelola department

**Batasan:**
- Hanya dapat mengelola data dalam organization sendiri
- Tidak dapat mengakses data organization lain

#### 2. Karyawan
**Hak Akses:**
- Melakukan absensi (Check In/Out)
- Melihat dashboard pribadi
- Mengajukan izin, cuti, dan lembur
- Melihat riwayat absensi sendiri
- Update profil pribadi
- Melihat kalender absensi
- Melihat saldo cuti

**Batasan:**
- Tidak dapat melihat data karyawan lain
- Tidak dapat approval izin/lembur
- Tidak dapat mengakses laporan lengkap
- Tidak dapat mengubah pengaturan sistem

### Tabel Hak Akses

| Fitur | Admin | Karyawan |
|-------|-------|----------|
| Departments | ✅ CRUD | ❌ |
| Users/Karyawan | ✅ CRUD | ❌ |
| Attendances | ✅ View All | ✅ Self Only |
| Shifts | ✅ CRUD | ✅ View |
| Locations | ✅ CRUD | ✅ View |
| Leaves (Izin/Cuti) | ✅ Approve/Reject | ✅ Request |
| Overtime (Lembur) | ✅ Approve/Reject | ✅ View |
| Reports | ✅ All Reports | ❌ |
| Holidays | ✅ CRUD | ✅ View |

---

## 4. DASHBOARD ADMIN

### Akses Dashboard

Login sebagai Admin → Akan langsung masuk ke Dashboard

### Komponen Dashboard

#### 1. Widget Statistik (Admin)

**Dashboard Stats:**
- Total User
- Total Karyawan
- Check In Hari Ini
- Check Out Hari Ini
- Absensi Hari Ini
- Absensi Minggu Ini

#### 2. Chart & Grafik
- Trend absensi bulanan
- Perbandingan tepat waktu vs terlambat

#### 3. Quick Access
- Menu navigasi cepat
- Search global (Cmd/Ctrl + K)

### Navigasi Menu Admin

**Menu Sidebar:**
```
📁 Absensi
  ├─ Data Absensi
  ├─ Kalender Absensi
  ├─ Laporan Keterlambatan
  ├─ Laporan Rekap Bulanan
  └─ Laporan Lembur

📁 Manajemen User
  ├─ Data User
  ├─ Data Shift
  ├─ Data Izin/Cuti
  ├─ Data Lembur
  └─ Data Department

📁 Pengaturan
  ├─ Lokasi Absensi
  └─ Hari Libur

📁 Bantuan
  ├─ FAQ
  ├─ Contact
  └─ Manual Book
```

---

## 5. DASHBOARD KARYAWAN

### Akses Dashboard Karyawan

Login sebagai Karyawan → Klik menu **Dashboard**

### Komponen Dashboard Karyawan

#### Widget Statistik Karyawan
- **Kehadiran Bulan Ini:** Total hari masuk & jumlah terlambat
- **Saldo Cuti:** Sisa cuti dari jatah tahunan (12 hari)
- **Overtime Bulan Ini:** Total jam lembur yang approved
- **Check-in Hari Ini:** Status sudah/belum check-in + jam

#### Riwayat Absensi
- 30 data absensi terakhir
- Export ke Excel/PDF

#### Status Hari Ini
- Status check in & check out
- Lokasi absensi
- Foto bukti

---

## 6. MANAJEMEN USER

### Menu: Data User

**Path:** Admin Panel → Manajemen User → Data User

### Menambah User Baru

1. Klik tombol **New User**
2. Isi form:
   - **Name:** Nama lengkap karyawan
   - **Username:** Username untuk login (unique)
   - **NIK:** Nomor Induk Karyawan
   - **NIP:** Nomor Induk Pegawai (opsional)
   - **Email:** Email karyawan (unique)
   - **Password:** Password login
   - **Role:** Pilih role (admin/karyawan)
   - **Organization:** Pilih organization (untuk admin)
   - **Department:** Pilih department
   - **Shift:** Pilih shift kerja
3. Klik **Create**

### Mengedit User

1. Klik tombol **Edit** pada user
2. Ubah data yang diperlukan
3. Klik **Save Changes**

### Menghapus User

1. Klik tombol **Delete** pada user
2. Konfirmasi penghapusan
3. Data user dan semua relasinya akan terhapus

### Import User (Bulk)

1. Klik tombol **Import Users**
2. Download template Excel
3. Isi data sesuai template:
   - name, username, email, password, nik, nip, role, shift_id, department_id, organization_id
4. Upload file Excel
5. Sistem akan validasi dan import

### Export User

1. Pilih user yang ingin di-export (atau all)
2. Klik **Export** → Pilih Excel/PDF
3. File akan terdownload

---

## 7. MANAJEMEN ABSENSI

### Menu: Data Absensi

**Path:** Admin Panel → Absensi → Data Absensi

### Cara Karyawan Melakukan Absensi

#### Check In (Absen Masuk)

1. Buka halaman: `/attendance`
2. Pastikan GPS aktif
3. Pastikan berada dalam radius lokasi absensi
4. Klik tombol **Ambil Foto**
5. Posisikan wajah di depan kamera
6. Klik tombol merah untuk capture foto
7. Review foto, jika OK klik **Check In**
8. Sistem akan validasi:
   - GPS dalam radius? ✓
   - Sudah check in hari ini? ✗
   - Ada foto? ✓
9. Jika valid → Sukses Check In!

#### Check Out (Absen Pulang)

1. Buka halaman: `/attendance`
2. Pastikan sudah check in sebelumnya
3. Ulangi langkah seperti check in
4. Klik **Check Out**
5. Sukses Check Out!

### Validasi Absensi

#### Validasi Geofencing
- Sistem cek apakah koordinat GPS karyawan dalam radius lokasi
- Rumus: Haversine formula
- Toleransi: Sesuai radius yang diatur (default 200m)

#### Validasi Waktu
- **Tepat Waktu:** Check in sebelum jam masuk + toleransi
- **Terlambat:** Check in setelah jam masuk + toleransi
- Toleransi default: 15 menit

#### Validasi Foto
- Wajib ada foto
- Format: JPEG/PNG
- Disimpan di storage

### Melihat Data Absensi (Admin)

1. Buka **Data Absensi**
2. Gunakan filter:
   - **Tanggal:** Range tanggal
   - **User:** Karyawan tertentu
   - **Tipe:** Check In / Check Out
   - **Status:** Tepat waktu / Terlambat
3. Lihat detail dengan klik **View**
4. Export dengan tombol **Export Excel/PDF**

### Edit/Hapus Absensi

**Catatan:** Hanya admin yang bisa edit/hapus

1. Klik **Edit** pada data absensi
2. Ubah data (waktu, lokasi, foto)
3. Save changes
4. Untuk hapus, klik **Delete** → Konfirmasi

---

## 8. SHIFT & JADWAL

### Menu: Data Shift

**Path:** Admin Panel → Manajemen User → Data Shift

### Membuat Shift Baru

1. Klik **New Shift**
2. Isi form:
   - **Nama Shift:** Contoh: "Shift Pagi", "Shift Siang"
   - **Jam Masuk:** Contoh: 08:00
   - **Jam Pulang:** Contoh: 17:00
   - **Toleransi Keterlambatan:** Dalam menit (contoh: 15)
3. Klik **Create**

### Contoh Shift

| Nama Shift | Jam Masuk | Jam Pulang | Toleransi |
|------------|-----------|------------|-----------|
| Shift Pagi | 08:00 | 17:00 | 15 menit |
| Shift Siang | 13:00 | 22:00 | 10 menit |
| Shift Malam | 22:00 | 07:00 | 20 menit |

### Assign Shift ke Karyawan

1. Buka **Data User**
2. Edit user yang ingin diubah shiftnya
3. Pilih **Shift** dari dropdown
4. Save

### Toleransi Keterlambatan

**Cara Kerja:**
- Jika toleransi = 15 menit
- Jam masuk = 08:00
- Maka check in sampai jam 08:15 masih dianggap **Tepat Waktu**
- Check in jam 08:16 atau lebih = **Terlambat**

---

## 9. LOKASI ABSENSI

### Menu: Lokasi Absensi

**Path:** Admin Panel → Pengaturan → Lokasi Absensi

### Menambah Lokasi Baru

1. Klik **New Attendance Location**
2. Isi form:
   - **Name:** Nama lokasi (contoh: "Kantor Pusat")
   - **Latitude:** Klik di map atau input manual
   - **Longitude:** Klik di map atau input manual
   - **Radius:** Dalam meter (contoh: 200)
3. **Cara pilih koordinat di map:**
   - Zoom ke lokasi yang diinginkan
   - Klik di map → Marker akan muncul
   - Lat/Lng otomatis terisi
4. Klik **Create**

### Geofencing

**Apa itu Geofencing?**
- Virtual boundary di sekitar lokasi
- Karyawan hanya bisa absen dalam radius tertentu

**Contoh:**
```
Lokasi: Kantor Pusat
Koordinat: -6.200000, 106.816666
Radius: 200 meter

→ Karyawan bisa absen dalam radius 200m dari koordinat tersebut
```

### Testing Lokasi

1. Buka halaman absensi: `/attendance`
2. Lihat peta yang muncul
3. Marker hijau = Lokasi kantor
4. Lingkaran biru = Radius geofencing
5. Pin merah = Lokasi Anda saat ini
6. Jika pin merah dalam lingkaran biru → Bisa absen ✓

---

## 10. IZIN & CUTI

### Jenis Izin/Cuti

1. **Sakit:** Izin sakit dengan/tanpa surat dokter
2. **Izin:** Izin karena keperluan pribadi
3. **Cuti:** Cuti tahunan (jatah 12 hari/tahun)

### Cara Karyawan Mengajukan Izin

#### Via Dashboard Karyawan

1. Login sebagai karyawan
2. Klik menu **Pengajuan Izin**
3. Klik tombol **Buat Pengajuan Baru**
4. Isi form:
   - **Jenis:** Pilih Sakit/Izin/Cuti
   - **Dari Tanggal:** Tanggal mulai
   - **Sampai Tanggal:** Tanggal selesai
   - **Total Hari:** Auto calculate
   - **Alasan:** Jelaskan alasan izin
   - **Dokumen:** Upload surat (opsional)
5. Klik **Ajukan**
6. Status: **Pending** (menunggu approval)

### Cara Admin Approve/Reject

1. Login sebagai admin
2. Buka **Data Izin/Cuti**
3. Filter status **Pending**
4. Klik **Edit** pada pengajuan
5. Ubah **Status** menjadi:
   - **Approved:** Disetujui
   - **Rejected:** Ditolak
6. Isi **Catatan** (opsional)
7. Klik **Save**
8. Karyawan akan mendapat notifikasi

### Saldo Cuti

**Aturan:**
- Setiap karyawan: 12 hari cuti/tahun
- Cuti akan mengurangi saldo
- Lihat saldo di dashboard karyawan
- Formula: 12 - (total hari cuti approved)

---

## 11. LEMBUR (OVERTIME)

### Menu: Data Lembur

**Path:** Admin Panel → Manajemen User → Data Lembur

### Cara Karyawan Request Lembur

**Catatan:** Request lembur dilakukan oleh admin untuk karyawan

### Cara Admin Input Lembur

1. Buka **Data Lembur**
2. Klik **New Overtime**
3. Isi form:
   - **User:** Pilih karyawan
   - **Tanggal:** Tanggal lembur
   - **Waktu Mulai:** Contoh: 18:00
   - **Waktu Selesai:** Contoh: 22:00
   - **Durasi:** Auto calculate (dalam menit)
   - **Multiplier:** 1.5x / 2x / 3x
   - **Status:** Pending/Approved/Rejected
   - **Catatan:** Detail pekerjaan lembur
4. Klik **Create**

### Multiplier Lembur

| Jenis Lembur | Multiplier |
|--------------|------------|
| Hari Biasa | 1.5x |
| Weekend | 2x |
| Hari Libur Nasional | 3x |

### Laporan Lembur

1. Buka **Laporan Lembur**
2. Filter:
   - Tanggal mulai & akhir
   - Karyawan (opsional)
   - Status
3. Klik **Generate Report**
4. Export ke Excel/PDF

---

## 12. LAPORAN

### Jenis Laporan

#### 1. Laporan Keterlambatan

**Path:** Absensi → Laporan Keterlambatan

**Fitur:**
- Filter tanggal
- Filter department
- Filter user
- Tampilkan data terlambat
- Total menit terlambat
- Export Excel/PDF

**Cara Pakai:**
1. Pilih periode (tanggal mulai - akhir)
2. Pilih department (opsional)
3. Klik **Generate Report**
4. Lihat tabel hasil
5. Export jika perlu

#### 2. Laporan Rekap Bulanan

**Path:** Absensi → Laporan Rekap Bulanan

**Fitur:**
- Rekap per karyawan
- Total hari kerja
- Total hadir
- Total terlambat
- Total alpha
- Total izin
- Persentase kehadiran

**Cara Pakai:**
1. Pilih bulan & tahun
2. Pilih department (opsional)
3. Klik **Generate Report**
4. Lihat rekap lengkap
5. Export Excel/PDF

#### 3. Laporan Lembur

**Path:** Absensi → Laporan Lembur

**Fitur:**
- Daftar lembur karyawan
- Total jam lembur
- Multiplier
- Status approval
- Export data

#### 4. Kalender Absensi

**Path:** Absensi → Kalender Absensi

**Fitur:**
- Tampilan kalender visual
- Warna berbeda untuk status:
  - 🟢 Hijau: Tepat waktu
  - 🟡 Kuning: Terlambat
  - 🔴 Merah: Alpha
  - 🔵 Biru: Izin/Cuti
- Navigasi bulan
- Filter per user (admin)
- Summary statistik

---

## 13. NOTIFIKASI

### Jenis Notifikasi Email

#### 1. Check-In Reminder
- **Waktu:** Setiap hari jam 08:30
- **Kepada:** Karyawan yang belum check-in
- **Isi:** Reminder untuk absen masuk

#### 2. Check-Out Reminder
- **Waktu:** Setiap 5 menit, antara 14:00-18:00
- **Kepada:** Karyawan yang sudah check-in tapi belum check-out
- **Isi:** Reminder untuk absen pulang

#### 3. Late Check-In Notification
- **Waktu:** Real-time saat terlambat
- **Kepada:** Admin
- **Isi:** Info karyawan yang terlambat + durasi

#### 4. Leave Status Notification
- **Waktu:** Real-time saat status berubah
- **Kepada:** Karyawan yang mengajukan
- **Isi:** Approved/Rejected + catatan

#### 5. Upcoming Leave Reminder
- **Waktu:** Setiap hari jam 17:00
- **Kepada:** Karyawan & Admin
- **Isi:** Info cuti/izin yang mulai besok

#### 6. Weekly Summary
- **Waktu:** Setiap Senin jam 08:00
- **Kepada:** Admin
- **Isi:** Rekap kehadiran minggu lalu

### Setting Notifikasi

**File:** `routes/console.php`

```php
// Scheduled Tasks
Schedule::command('attendance:send-checkin-reminder')
    ->dailyAt('08:30');

Schedule::command('attendance:send-checkout-reminder')
    ->everyFiveMinutes()
    ->between('14:00', '18:00');

Schedule::command('attendance:send-weekly-summary')
    ->weeklyOn(1, '08:00'); // Monday

Schedule::command('attendance:send-upcoming-leave-notification')
    ->dailyAt('17:00');
```

---

## 14. MULTI-TENANCY

### Apa itu Multi-Tenancy?

Sistem dapat mengelola **multiple organizations** dalam satu aplikasi.

**Contoh:**
- Organization 1: PT. ABC Indonesia
- Organization 2: CV. XYZ Tekno
- Organization 3: Toko Elektronik 123

### Cara Kerja

1. Super Admin membuat organization
2. Assign admin ke organization
3. Admin hanya bisa manage user/data di organization sendiri
4. Data ter-isolasi per organization

### Membuat Organization (Super Admin)

1. Login sebagai Super Admin
2. Buka **Organizations**
3. Klik **New Organization**
4. Isi form:
   - **Name:** Nama perusahaan
   - **Type:** Jenis bisnis
   - **Email:** Email perusahaan
   - **Phone:** Telepon
   - **Address:** Alamat
   - **Logo:** Upload logo (opsional)
   - **Max Users:** Batas user
   - **Is Active:** Aktif/Tidak
5. Klik **Create**

### Assign User ke Organization

1. Buka **Data User**
2. Edit user yang ingin di-assign
3. Pilih **Organization** dari dropdown
4. Save

### Brand Dinamis

Brand name di admin panel akan otomatis mengikuti nama organization:
- Organization: "Minoru Coffee" → Brand: "Minoru Coffee"
- Organization: "PT. ABC" → Brand: "PT. ABC"

---

## 15. FAQ

### Pertanyaan Umum

**Q: Bagaimana cara login?**  
A: Gunakan username/email dan password. Admin ke `/admin`, Karyawan ke `/login`.

**Q: Lupa password?**  
A: Hubungi admin untuk reset password.

**Q: Kenapa tidak bisa absen?**  
A: Pastikan:
- GPS aktif
- Dalam radius lokasi
- Belum check-in hari ini (untuk check-in)
- Sudah check-in (untuk check-out)

**Q: Bagaimana cara export data?**  
A: Setiap tabel ada tombol Export (Excel/PDF) di atas.

**Q: Berapa jatah cuti?**  
A: 12 hari per tahun.

**Q: Bagaimana cara mengubah shift?**  
A: Admin bisa edit user → ubah shift.

**Q: Radius geofencing bisa diubah?**  
A: Ya, admin bisa edit lokasi absensi → ubah radius.

**Q: Data bisa di-import bulk?**  
A: Ya, ada fitur import user via Excel.

**Q: Apakah ada aplikasi mobile?**  
A: Saat ini web-based, tapi responsive untuk mobile browser.

**Q: Bagaimana cara backup data?**  
A: Admin bisa export semua data ke Excel/PDF.

---

## 16. TROUBLESHOOTING

### Masalah Umum & Solusi

#### Error 403 Forbidden

**Penyebab:**
- User tidak punya akses admin
- Middleware authentication error

**Solusi:**
1. Pastikan user role = admin/super_admin
2. Jalankan: `php artisan admin:check --fix`
3. Clear cache: `php artisan cache:clear`

#### Menu Tidak Muncul

**Penyebab:**
- Role tidak sesuai
- Navigation permissions

**Solusi:**
1. Cek role user
2. Logout → Login ulang
3. Clear browser cache

#### GPS Tidak Akurat

**Penyebab:**
- GPS device tidak aktif
- Indoor/blocked signal

**Solusi:**
1. Aktifkan GPS di device
2. Allow location di browser
3. Coba di outdoor
4. Refresh halaman

#### Kamera Tidak Muncul

**Penyebab:**
- Browser tidak allow camera
- HTTPS required

**Solusi:**
1. Klik "Allow" saat browser minta akses kamera
2. Pastikan menggunakan HTTPS
3. Cek browser settings → Privacy → Camera

#### Upload Foto Gagal

**Penyebab:**
- File terlalu besar
- Format tidak didukung

**Solusi:**
1. Compress foto
2. Gunakan format JPEG/PNG
3. Max size: 2MB

#### Export Tidak Berfungsi

**Penyebab:**
- Tidak ada data
- Library error

**Solusi:**
1. Pastikan ada data untuk di-export
2. Coba filter lebih spesifik
3. Clear cache browser

#### Notifikasi Tidak Terkirim

**Penyebab:**
- Cron job tidak jalan
- SMTP error

**Solusi:**
1. Setup cron job di server
2. Cek konfigurasi email di `.env`
3. Test dengan: `php artisan schedule:run`

---

## 17. KONTAK SUPPORT

### Hubungi Kami

Jika mengalami kesulitan atau butuh bantuan lebih lanjut:

#### 📧 Email
**yusrilmahendri.yusril@gmail.com**
- Respon: 1x24 jam kerja
- Untuk pertanyaan detail & dokumentasi

#### 📞 Telepon
**085161597598**
- Senin - Jumat: 09:00 - 17:00 WIB
- Sabtu: 09:00 - 13:00 WIB

#### 💬 WhatsApp
**085161597598**
- Available: 24/7
- Respon cepat: 1-2 jam kerja
- [Klik untuk chat](https://wa.me/6285161597598)

#### 🌐 Website
Akses halaman bantuan di sistem:
- FAQ: `/admin/f-a-q`
- Contact: `/admin/contact`

### Jam Operasional Support

| Hari | Waktu |
|------|-------|
| Senin - Jumat | 09:00 - 17:00 WIB |
| Sabtu | 09:00 - 13:00 WIB |
| Minggu | Tutup |

**Emergency Support (24/7):**  
Untuk masalah kritis, hubungi WhatsApp: 085161597598

---

## 📚 LAMPIRAN

### Shortcut Keyboard (Admin Panel)

| Shortcut | Fungsi |
|----------|--------|
| `Cmd/Ctrl + K` | Global Search |
| `Cmd/Ctrl + /` | Toggle Sidebar |
| `Esc` | Close Modal |

### Status Color Code

| Status | Warna | Keterangan |
|--------|-------|------------|
| Tepat Waktu | 🟢 Hijau | Check-in sesuai waktu |
| Terlambat | 🟡 Kuning | Check-in terlambat |
| Alpha | 🔴 Merah | Tidak hadir |
| Izin/Cuti | 🔵 Biru | Ada izin approved |

### File Locations

```
/app/Models/          → Model database
/app/Filament/        → Admin panel (Resources, Pages, Widgets)
/app/Http/Controllers/ → Controller karyawan
/resources/views/     → Blade templates
/database/migrations/ → Database schema
/storage/app/public/  → Uploaded files
```

### Useful Commands

```bash
# Clear all cache
php artisan optimize:clear

# Check admin access
php artisan admin:check --fix

# Run scheduled tasks
php artisan schedule:run

# Create admin user
php artisan make:filament-user

# Import users
php artisan import:users

# Generate reports
php artisan reports:generate
```

---

## 🎓 PELATIHAN & ONBOARDING

### Pelatihan Admin (1-2 Jam)

**Materi:**
1. Login & navigasi dashboard
2. Manajemen user & import bulk
3. Setting shift & lokasi
4. Approval izin & lembur
5. Generate & export laporan
6. Troubleshooting dasar

### Pelatihan Karyawan (30 Menit)

**Materi:**
1. Cara login
2. Cara absen (check in/out)
3. Pengajuan izin/cuti
4. Lihat riwayat absensi
5. Update profil

### Request Pelatihan

Hubungi: **yusrilmahendri.yusril@gmail.com** atau **085161597598**

---

## 📝 CHANGELOG

### Version 2.0 (Current)
- ✅ Multi-tenancy support
- ✅ Brand dinamis per organization
- ✅ FAQ & Contact pages
- ✅ Notifikasi otomatis
- ✅ Kalender absensi
- ✅ Dashboard karyawan
- ✅ Export Excel/PDF
- ✅ Geofencing dengan map picker

### Version 1.0
- Basic attendance system
- Admin panel
- User management
- Shift management

---

## 📄 LISENSI & COPYRIGHT

© 2026 Sistem Presensi. All rights reserved.

**Developed by:** Yusril Mahendri  
**Contact:** yusrilmahendri.yusril@gmail.com | 085161597598

---

**END OF MANUAL BOOK**

---

*Terima kasih telah menggunakan Sistem Presensi!*  
*Untuk update dan bantuan lebih lanjut, jangan ragu menghubungi tim support kami.*

📧 yusrilmahendri.yusril@gmail.com  
📞 085161597598  
💬 https://wa.me/6285161597598
