# 📧 PANDUAN LENGKAP NOTIFIKASI EMAIL - IZIN, LEMBUR & PERGANTIAN SHIFT

## 📋 Daftar Notifikasi Email yang Tersedia

### ✅ 3 Jenis Pengajuan dengan Email Notifikasi

| No | Pengajuan | Karyawan Dapat Email Saat | File Notifikasi |
|----|-----------|---------------------------|-----------------|
| 1️⃣ | **Izin/Cuti** | ✅ Disetujui<br>❌ Ditolak | `LeaveStatusNotification.php` |
| 2️⃣ | **Lembur** | ✅ Disetujui<br>❌ Ditolak | `OvertimeApprovalNotification.php` |
| 3️⃣ | **Pergantian Shift** | ✅ Disetujui<br>❌ Ditolak | `ShiftChangeStatusNotification.php` |

---

## 1️⃣ NOTIFIKASI IZIN/CUTI

### 📨 Kapan Email Terkirim?

**Trigger:** Admin approve/reject pengajuan izin di menu **Manajemen Karyawan → Izin/Cuti**

### 📄 Template Email

#### ✅ Ketika DISETUJUI:
```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: Status Cuti Disetujui

Halo Budi Santoso,

Permohonan cuti Anda telah disetujui.

Detail:
• Tanggal: 27/12/2025 - 31/12/2025
• Jumlah Hari: 5 hari
• Alasan: Liburan keluarga

Catatan: Selamat menikmati cuti Anda!

[Lihat Detail]
```

#### ❌ Ketika DITOLAK:
```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: Status Cuti Ditolak

Halo Budi Santoso,

Permohonan cuti Anda telah ditolak.

Detail:
• Tanggal: 27/12/2025 - 31/12/2025
• Jumlah Hari: 5 hari
• Alasan: Liburan keluarga

Catatan: Maaf, periode cuti bertepatan dengan peak season. 
Silakan ajukan cuti di periode lain.

[Lihat Detail]
```

### 🔧 Cara Kerja di Sistem

**File:** `app/Filament/Resources/LeaveResource.php`

```php
// Action Approve
Tables\Actions\Action::make('approve')
    ->action(function ($record, array $data) {
        $record->update([
            'status' => 'approved',
            'notes' => $data['notes'],
        ]);
        
        // ✅ Kirim email ke karyawan
        $record->user->notify(new \App\Notifications\LeaveStatusNotification(
            $record,
            'approved',
            $data['notes']
        ));
    });

// Action Reject
Tables\Actions\Action::make('reject')
    ->action(function ($record, array $data) {
        $record->update([
            'status' => 'rejected',
            'notes' => $data['notes'],
        ]);
        
        // ❌ Kirim email ke karyawan
        $record->user->notify(new \App\Notifications\LeaveStatusNotification(
            $record,
            'rejected',
            $data['notes']
        ));
    });
```

---

## 2️⃣ NOTIFIKASI LEMBUR

### 📨 Kapan Email Terkirim?

**Trigger:** Admin approve/reject pengajuan lembur di menu **Manajemen Karyawan → Lembur**

### 📄 Template Email

#### ✅ Ketika DISETUJUI:
```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: Lembur Disetujui

Halo Budi Santoso,

Lembur Anda telah disetujui.

Detail:
• Tanggal: 26/12/2025
• Durasi: 3 jam
• Waktu: 17:00 - 20:00

Catatan: Terima kasih atas dedikasi Anda!

[Lihat Detail]
```

#### ❌ Ketika DITOLAK:
```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: Lembur Ditolak

Halo Budi Santoso,

Lembur Anda telah ditolak.

Detail:
• Tanggal: 26/12/2025
• Durasi: 3 jam
• Waktu: 17:00 - 20:00

Catatan: Pekerjaan dapat diselesaikan di hari berikutnya.

[Lihat Detail]
```

### 🔧 Cara Kerja di Sistem

**File:** `app/Filament/Resources/OvertimeResource.php`

```php
// Action Approve
Tables\Actions\Action::make('approve')
    ->action(function ($record, array $data) {
        $record->update([
            'status' => 'approved',
            'notes' => $data['notes'],
        ]);
        
        // ✅ Kirim email ke karyawan
        $record->user->notify(new \App\Notifications\OvertimeApprovalNotification(
            $record,
            'approved',
            $data['notes']
        ));
    });

// Action Reject
Tables\Actions\Action::make('reject')
    ->action(function ($record, array $data) {
        $record->update([
            'status' => 'rejected',
            'notes' => $data['notes'],
        ]);
        
        // ❌ Kirim email ke karyawan
        $record->user->notify(new \App\Notifications\OvertimeApprovalNotification(
            $record,
            'rejected',
            $data['notes']
        ));
    });
```

---

## 3️⃣ NOTIFIKASI PERGANTIAN SHIFT (BARU! ✨)

### 📨 Kapan Email Terkirim?

**Trigger:** Admin approve/reject pengajuan pergantian shift di menu **Manajemen Karyawan → Pergantian Shift**

### 📄 Template Email

#### ✅ Ketika DISETUJUI:
```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: Pergantian Shift Disetujui ✅

Halo Budi Santoso,

Pengajuan pergantian shift Anda telah disetujui.

🎉 Selamat! Permintaan pergantian shift Anda telah disetujui.

Detail Pergantian:
• Shift Lama: Shift Pagi (08:00 - 16:00)
• Shift Baru: Shift Malam (16:00 - 00:00)
• Efektif Mulai: 01 Januari 2026

Shift Anda akan otomatis berubah pada tanggal yang ditentukan.

Catatan Admin: Disetujui sesuai kebutuhan operasional.

[Lihat Detail]
```

#### ❌ Ketika DITOLAK:
```
From: PioneerHadir System <system@company.com>
To: karyawan@gmail.com
Subject: Pergantian Shift Ditolak ❌

Halo Budi Santoso,

Pengajuan pergantian shift Anda telah ditolak.

😔 Maaf, permintaan pergantian shift Anda tidak dapat disetujui.

Detail Pengajuan:
• Dari Shift: Shift Pagi (08:00 - 16:00)
• Ke Shift: Shift Malam (16:00 - 00:00)
• Tanggal Efektif: 01 Januari 2026
• Alasan Anda: Kebutuhan keluarga

Catatan Admin:
Shift malam sudah penuh untuk periode tersebut. Silakan ajukan 
kembali untuk bulan berikutnya atau pilih shift lain.

[Lihat Detail]
```

### 🔧 Cara Kerja di Sistem

**File:** `app/Filament/Resources/ShiftChangeRequestResource.php`

```php
// Action Approve
Tables\Actions\Action::make('approve')
    ->form([
        Forms\Components\Textarea::make('approval_notes')
            ->label('Catatan (Opsional)')
            ->rows(2),
    ])
    ->action(function ($record, array $data) {
        $record->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $data['approval_notes'] ?? null,
        ]);
        
        // Update shift karyawan
        $record->user->update([
            'shift_id' => $record->requested_shift_id,
        ]);
        
        // ✅ Kirim email ke karyawan
        $record->user->notify(new \App\Notifications\ShiftChangeStatusNotification(
            $record,
            'approved',
            $data['approval_notes'] ?? null
        ));
    });

// Action Reject
Tables\Actions\Action::make('reject')
    ->form([
        Forms\Components\Textarea::make('rejection_notes')
            ->label('Alasan Penolakan')
            ->required()
            ->rows(3),
    ])
    ->action(function ($record, array $data) {
        $record->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $data['rejection_notes'],
        ]);
        
        // ❌ Kirim email ke karyawan
        $record->user->notify(new \App\Notifications\ShiftChangeStatusNotification(
            $record,
            'rejected',
            $data['rejection_notes']
        ));
    });
```

---

## 🎯 Flow Lengkap Email Notifikasi

### Skenario 1: Karyawan Mengajukan Izin

```
1. Karyawan ajukan izin via dashboard
   ↓
2. Admin terima notifikasi (di sistem)
   ↓
3. Admin review dan klik "Setujui" atau "Tolak"
   ↓
4. Admin isi catatan (wajib untuk tolak, opsional untuk setujui)
   ↓
5. Sistem update status di database
   ↓
6. Sistem kirim email ke karyawan via queue
   ↓
7. Karyawan terima email notifikasi
   ↓
8. Karyawan lihat detail di dashboard
```

### Skenario 2: Karyawan Mengajukan Lembur

```
1. Karyawan checkout > jam maksimal
   ↓
2. Sistem auto-create overtime record
   ↓
3. Admin review di menu Lembur
   ↓
4. Admin klik "Setujui" atau "Tolak" + catatan
   ↓
5. Email terkirim ke karyawan
   ↓
6. Karyawan terima konfirmasi
```

### Skenario 3: Karyawan Mengajukan Pergantian Shift

```
1. Karyawan ajukan pergantian shift
   ↓
2. Admin review di menu Pergantian Shift
   ↓
3. Admin klik "Setujui" (shift auto update) atau "Tolak"
   ↓
4. Admin isi catatan
   ↓
5. Email terkirim ke karyawan
   ↓
6. Karyawan terima konfirmasi perubahan shift
```

---

## 🧪 Cara Testing Email Notifikasi

### Test 1: Notifikasi Izin/Cuti

```bash
php artisan tinker
```

```php
// Buat leave request
$user = \App\Models\User::where('role', 'employee')->first();

$leave = \App\Models\Leave::create([
    'user_id' => $user->id,
    'organization_id' => $user->organization_id,
    'start_date' => now()->addDays(7),
    'end_date' => now()->addDays(10),
    'days' => 4,
    'reason' => 'Testing notifikasi',
    'status' => 'pending'
]);

// Test kirim notifikasi approved
$leave->user->notify(new \App\Notifications\LeaveStatusNotification(
    $leave,
    'approved',
    'Selamat menikmati cuti!'
));

// Test kirim notifikasi rejected
$leave->user->notify(new \App\Notifications\LeaveStatusNotification(
    $leave,
    'rejected',
    'Periode cuti terlalu dekat dengan deadline project.'
));
```

### Test 2: Notifikasi Lembur

```php
// Buat overtime
$overtime = \App\Models\Overtime::create([
    'user_id' => $user->id,
    'organization_id' => $user->organization_id,
    'date' => now()->toDateString(),
    'start_time' => '17:00',
    'end_time' => '20:00',
    'reason' => 'Testing notifikasi lembur',
    'status' => 'pending'
]);

// Test approved
$overtime->user->notify(new \App\Notifications\OvertimeApprovalNotification(
    $overtime,
    'approved',
    'Terima kasih atas dedikasi Anda!'
));

// Test rejected
$overtime->user->notify(new \App\Notifications\OvertimeApprovalNotification(
    $overtime,
    'rejected',
    'Pekerjaan dapat diselesaikan besok.'
));
```

### Test 3: Notifikasi Pergantian Shift

```php
// Buat shift change request
$currentShift = \App\Models\Shift::first();
$requestedShift = \App\Models\Shift::skip(1)->first();

$shiftChange = \App\Models\ShiftChangeRequest::create([
    'user_id' => $user->id,
    'organization_id' => $user->organization_id,
    'current_shift_id' => $currentShift->id,
    'requested_shift_id' => $requestedShift->id,
    'effective_date' => now()->addWeeks(2),
    'reason' => 'Testing notifikasi shift',
    'status' => 'pending'
]);

// Test approved
$shiftChange->user->notify(new \App\Notifications\ShiftChangeStatusNotification(
    $shiftChange,
    'approved',
    'Disetujui sesuai kebutuhan operasional.'
));

// Test rejected
$shiftChange->user->notify(new \App\Notifications\ShiftChangeStatusNotification(
    $shiftChange,
    'rejected',
    'Shift malam sudah penuh untuk periode tersebut.'
));
```

---

## 📊 Monitoring Email Notifikasi

### Cek Queue Jobs

```sql
-- Lihat email yang sedang antri
SELECT * FROM jobs ORDER BY created_at DESC LIMIT 10;
```

### Cek Failed Jobs

```sql
-- Lihat email yang gagal terkirim
SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
```

### Cek Notifikasi di Database

```sql
-- Lihat semua notifikasi karyawan
SELECT 
    u.name AS karyawan,
    u.email,
    n.type,
    n.data,
    n.read_at,
    n.created_at
FROM notifications n
JOIN users u ON JSON_EXTRACT(n.notifiable_id, '$') = u.id
WHERE u.role = 'employee'
ORDER BY n.created_at DESC
LIMIT 20;
```

### Monitor Queue Worker

```bash
# Lihat process queue worker
ps aux | grep "queue:work"

# Restart queue worker
php artisan queue:restart
php artisan queue:work
```

---

## 🔧 Troubleshooting

### ❌ Email Tidak Terkirim

**Cek 1: Queue Worker Running?**
```bash
ps aux | grep queue:work
# Jika tidak ada, jalankan:
php artisan queue:work
```

**Cek 2: Email Karyawan Valid?**
```sql
SELECT id, name, email FROM users WHERE role = 'employee';
# Pastikan semua punya email
```

**Cek 3: SMTP Configured?**
```bash
grep MAIL_ .env
# Pastikan MAIL_MAILER=smtp (bukan log)
```

**Cek 4: Cek Log**
```bash
tail -f storage/logs/laravel.log
```

---

## 📋 Checklist Email Notifikasi Siap Production

- [ ] **Setup Email Sistem**
  - [ ] MAIL_MAILER=smtp
  - [ ] MAIL_HOST, MAIL_PORT configured
  - [ ] MAIL_USERNAME & MAIL_PASSWORD (App Password) set
  - [ ] Config cache cleared: `php artisan config:cache`

- [ ] **Queue Worker**
  - [ ] QUEUE_CONNECTION=database
  - [ ] Queue worker running (supervisor/systemd)
  - [ ] Auto-restart enabled

- [ ] **Testing Notifikasi**
  - [ ] Test izin disetujui ✅
  - [ ] Test izin ditolak ❌
  - [ ] Test lembur disetujui ✅
  - [ ] Test lembur ditolak ❌
  - [ ] Test shift change disetujui ✅
  - [ ] Test shift change ditolak ❌

- [ ] **Email Karyawan**
  - [ ] Semua karyawan punya email valid
  - [ ] Email masuk inbox (bukan spam)

---

## 💡 Best Practices

### 1. Selalu Berikan Catatan yang Jelas

**❌ Buruk:**
```
Catatan: Ditolak
```

**✅ Baik:**
```
Catatan: Maaf, periode cuti bertepatan dengan peak season kami. 
Silakan ajukan kembali untuk periode setelah tanggal 15 Januari.
Terima kasih atas pengertiannya.
```

### 2. Gunakan Tone Profesional tapi Ramah

**Untuk Approval:**
- "Selamat! Pengajuan Anda disetujui."
- "Terima kasih atas dedikasi Anda."

**Untuk Rejection:**
- "Maaf, saat ini belum dapat kami setujui."
- "Silakan ajukan kembali di periode lain."

### 3. Monitor Email Delivery Rate

```php
// Cek berapa email terkirim hari ini
\DB::table('jobs')
    ->whereDate('created_at', today())
    ->count();

// Cek berapa email gagal
\DB::table('failed_jobs')
    ->whereDate('failed_at', today())
    ->count();
```

---

**Ringkasan:**
- ✅ **3 jenis notifikasi email** sudah siap: Izin, Lembur, Pergantian Shift
- ✅ Email otomatis terkirim saat admin approve/reject
- ✅ Karyawan dapat notifikasi lengkap dengan detail & catatan admin
- ✅ Semua notifikasi masuk queue untuk pengiriman background

---

**Update:** 26 Desember 2025  
**Status:** Production Ready ✅
