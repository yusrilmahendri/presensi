# IMPLEMENTATION GUIDE - Sprint 1 to 4
## Sistem Presensi - Full Feature Implementation

### ✅ COMPLETED

#### Database Structure
- ✅ `holidays` table - Calendar & holiday management
- ✅ `departments` table - Department hierarchy
- ✅ `overtimes` table - Overtime tracking & approval
- ✅ `audit_logs` table - Activity tracking
- ✅ `notifications` table - Built-in Laravel notifications
- ✅ Added `department_id` to users
- ✅ Added `photo`, `device_info`, `accuracy` to attendances

#### Models Created
- ✅ Holiday model with isHoliday() helper
- ✅ Department model with hierarchical structure
- ✅ Overtime model with approval workflow
- ✅ AuditLog model with auto-logging helper
- ✅ User model updated with new relationships

---

## 🚧 NEXT STEPS - Implementation Guide

### SPRINT 1: Foundation (Week 1-2)

#### 1. Notification System Implementation

**A. Update User Model**
```php
// app/Models/User.php
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable; // Add this trait
    
    // Email notification preferences
    public function receivesBroadcastNotificationsOn()
    {
        return 'users.'.$this->id;
    }
}
```

**B. Implement Notifications**
```bash
# Already created:
- LeaveStatusNotification
- OvertimeApprovalNotification  
- LateCheckInNotification

# Implement each notification with:
- toMail() method
- toDatabase() method
- Custom message templates
```

**C. Send Notifications**
```php
// When leave approved:
$leave->user->notify(new LeaveStatusNotification($leave, 'approved'));

// When overtime submitted:
$admin->notify(new OvertimeApprovalNotification($overtime));

// Late check-in (scheduled command):
$user->notify(new LateCheckInNotification($attendance));
```

**D. Create Scheduled Commands**
```bash
php artisan make:command SendLateCheckInReminders
php artisan make:command SendDailyAttendanceReport
```

Register in `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('attendance:check-late')->dailyAt('09:30');
    $schedule->command('attendance:daily-report')->dailyAt('17:00');
}
```

---

#### 2. Holiday & Calendar Management

**A. Create Filament Resource**
```bash
php artisan make:filament-resource Holiday
```

**B. HolidayResource Configuration**
- Form: name, date, type (select), is_recurring checkbox
- Table: date, name, type badge, organization
- Filters: type, date range, organization (for super admin)
- Policy: Only admin (not super admin) can manage

**C. Calendar Widget**
```bash
php artisan make:filament-widget HolidayCalendar --view
```

Display upcoming holidays in dashboard.

**D. Integration with Attendance**
Update AttendanceController:
```php
public function store(Request $request)
{
    if (Holiday::isHoliday(now(), auth()->user()->organization_id)) {
        return back()->with('error', 'Hari ini libur, tidak perlu absen');
    }
    
    // Continue with normal attendance...
}
```

---

#### 3. Reporting & Analytics

**A. Create Report Widgets**
```bash
php artisan make:filament-widget AttendanceReportChart --chart
php artisan make:filament-widget LeaveUtilizationWidget --stats
php artisan make:filament-widget DepartmentAttendanceWidget --table
```

**B. Export Functionality**
Update AttendanceResource:
```php
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\ExportColumn;

public static function table(Table $table): Table
{
    return $table
        ->headerActions([
            ExportAction::make()
                ->exporter(AttendanceExporter::class)
        ]);
}
```

Create Exporter:
```bash
php artisan make:filament-exporter Attendance
```

**C. Custom Report Page**
```bash
php artisan make:filament-page AttendanceReport
```

Features:
- Date range picker
- Department filter
- User filter
- Export to Excel/PDF
- Charts: attendance rate, late trend, department comparison

---

### SPRINT 2: Enhancement (Week 3-4)

#### 4. Overtime Tracking

**A. Create OvertimeResource**
```bash
php artisan make:filament-resource Overtime
```

**B. Features**
- Auto-detect overtime from attendance (if end time > shift end time)
- Approval workflow (pending → approved/rejected)
- Email notification to admin
- Monthly overtime report per employee
- Overtime rate calculation (hours × multiplier)

**C. Auto-Detection Logic**
```php
// In Attendance observer or service
public function created(Attendance $attendance)
{
    if ($attendance->type === 'check_out') {
        $shift = $attendance->user->shift;
        $shiftEnd = Carbon::parse($shift->end_time);
        $actualEnd = Carbon::parse($attendance->attendance_time);
        
        if ($actualEnd->gt($shiftEnd)) {
            $overtimeMinutes = $actualEnd->diffInMinutes($shiftEnd);
            
            if ($overtimeMinutes >= 30) { // Minimum 30 minutes
                Overtime::create([
                    'user_id' => $attendance->user_id,
                    'attendance_id' => $attendance->id,
                    'date' => $attendance->attendance_time->toDateString(),
                    'start_time' => $shiftEnd,
                    'end_time' => $actualEnd,
                    'duration_minutes' => $overtimeMinutes,
                    'status' => 'pending',
                ]);
            }
        }
    }
}
```

---

#### 5. Bulk Import/Export

**A. Install Maatwebsite Excel** (already installed)

**B. Create Import Class**
```bash
php artisan make:import UsersImport --model=User
```

**C. Implement Import Logic**
```php
class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new User([
            'name' => $row['nama'],
            'nik' => $row['nik'],
            'email' => $row['email'],
            'organization_id' => auth()->user()->organization_id,
            'department_id' => Department::where('code', $row['kode_departemen'])->first()?->id,
            'role' => 'karyawan',
            'password' => Hash::make($row['nik']), // Default password = NIK
        ]);
    }
    
    public function rules(): array
    {
        return [
            'nama' => 'required',
            'nik' => 'required|unique:users,nik',
            'email' => 'required|email|unique:users,email',
        ];
    }
}
```

**D. Add Import Action to UserResource**
```php
protected function getHeaderActions(): array
{
    return [
        Action::make('import')
            ->label('Import Excel')
            ->form([
                FileUpload::make('file')
                    ->label('File Excel')
                    ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->required(),
            ])
            ->action(function (array $data) {
                Excel::import(new UsersImport, $data['file']);
                Notification::make()->success()->title('Import berhasil')->send();
            }),
    ];
}
```

---

#### 6. Photo Verification

**A. Update Attendance Form (already has photo column)**

**B. Karyawan View - Add Camera Capture**
```blade
<!-- resources/views/karyawan/attendance/check-in.blade.php -->
<div>
    <video id="video" width="320" height="240" autoplay></video>
    <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
    <button onclick="capturePhoto()">Ambil Foto</button>
    <input type="hidden" name="photo" id="photo-input">
</div>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');

navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
    });

function capturePhoto() {
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, 320, 240);
    const photoData = canvas.toDataURL('image/jpeg');
    document.getElementById('photo-input').value = photoData;
}
</script>
```

**C. Store Photo in Controller**
```php
public function checkIn(Request $request)
{
    $request->validate([
        'photo' => 'required',
        'latitude' => 'required',
        'longitude' => 'required',
    ]);
    
    // Save base64 photo to storage
    $photoData = $request->photo;
    $photo = str_replace('data:image/jpeg;base64,', '', $photoData);
    $photo = str_replace(' ', '+', $photo);
    $photoName = 'attendance_'.time().'.jpg';
    Storage::disk('public')->put('photos/'.$photoName, base64_decode($photo));
    
    Attendance::create([
        'user_id' => auth()->id(),
        'attendance_time' => now(),
        'type' => 'check_in',
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'photo' => 'photos/'.$photoName,
        'device_info' => $request->userAgent(),
    ]);
}
```

---

### SPRINT 3: Mobile (Week 5-6)

#### 7. PWA Configuration

**A. Install Laravel PWA Package**
```bash
composer require silviolleite/laravelpwa
php artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"
```

**B. Configure manifest.json**
```json
{
  "name": "Sistem Presensi",
  "short_name": "Presensi",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#667eea",
  "theme_color": "#764ba2",
  "icons": [
    {
      "src": "/images/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/images/icon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

**C. Service Worker for Offline**
```javascript
// public/serviceworker.js
const CACHE_NAME = 'presensi-v1';
const urlsToCache = [
  '/',
  '/dashboard',
  '/css/app.css',
  '/js/app.js',
  '/offline.html'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
      .catch(() => caches.match('/offline.html'))
  );
});
```

**D. Add to Layout**
```blade
<!-- resources/views/layouts/app.blade.php -->
<head>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#764ba2">
    <link rel="apple-touch-icon" href="/images/icon-192.png">
</head>

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/serviceworker.js');
}
</script>
```

---

### SPRINT 4: Organization (Week 7-8)

#### 8. Department Management

**A. Create DepartmentResource** (Already have model)
```bash
php artisan make:filament-resource Department
```

**B. Hierarchical Structure**
```php
public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('name')->required(),
        TextInput::make('code')->unique(ignoreRecord: true),
        Select::make('parent_id')
            ->label('Parent Department')
            ->relationship('parent', 'name')
            ->searchable(),
        Select::make('manager_id')
            ->label('Department Manager')
            ->relationship('manager', 'name')
            ->searchable(),
        Textarea::make('description'),
        Toggle::make('is_active')->default(true),
    ]);
}
```

**C. Tree View Widget**
```bash
php artisan make:filament-widget DepartmentTree --view
```

---

#### 9. Audit Log System

**A. Create Trait for Auto-Logging**
```php
// app/Traits/HasAuditLog.php
trait HasAuditLog
{
    public static function bootHasAuditLog()
    {
        static::created(function ($model) {
            AuditLog::logActivity('created', $model, null, $model->toArray());
        });
        
        static::updated(function ($model) {
            AuditLog::logActivity('updated', $model, $model->getOriginal(), $model->getChanges());
        });
        
        static::deleted(function ($model) {
            AuditLog::logActivity('deleted', $model, $model->toArray(), null);
        });
    }
}
```

**B. Apply to Models**
```php
class User extends Authenticatable
{
    use HasAuditLog;
}

class Attendance extends Model
{
    use HasAuditLog;
}
```

**C. Create AuditLogResource**
```bash
php artisan make:filament-resource AuditLog
```

Features:
- Read-only table
- Filter by user, event, date
- Search by description
- Color-coded events (created=green, updated=blue, deleted=red)

---

#### 10. Payroll Report

**A. Create PayrollReport Page**
```bash
php artisan make:filament-page PayrollReport
```

**B. Calculate Work Days**
```php
public function calculatePayroll($userId, $month, $year)
{
    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();
    
    // Count working days (exclude weekends & holidays)
    $workingDays = 0;
    $current = $startDate->copy();
    
    while ($current->lte($endDate)) {
        if (!$current->isWeekend() && !Holiday::isHoliday($current, $userId)) {
            $workingDays++;
        }
        $current->addDay();
    }
    
    // Count actual attendance
    $attendances = Attendance::where('user_id', $userId)
        ->whereMonth('attendance_time', $month)
        ->whereYear('attendance_time', $year)
        ->where('type', 'check_in')
        ->count();
    
    // Count approved leaves
    $leaves = Leave::where('user_id', $userId)
        ->where('status', 'approved')
        ->whereMonth('start_date', $month)
        ->sum('days');
    
    // Count overtime hours
    $overtimeHours = Overtime::where('user_id', $userId)
        ->where('status', 'approved')
        ->whereMonth('date', $month)
        ->sum('duration_minutes') / 60;
    
    return [
        'working_days' => $workingDays,
        'present_days' => $attendances,
        'leave_days' => $leaves,
        'absent_days' => $workingDays - $attendances - $leaves,
        'overtime_hours' => round($overtimeHours, 2),
    ];
}
```

---

## 📋 FILAMENT RESOURCES TO CREATE

```bash
# Sprint 1
php artisan make:filament-resource Holiday
php artisan make:filament-page AttendanceReport

# Sprint 2
php artisan make:filament-resource Overtime
php artisan make:filament-resource Department

# Sprint 4
php artisan make:filament-resource AuditLog --view-only
php artisan make:filament-page PayrollReport
```

---

## 🎯 POLICIES TO CREATE

```bash
php artisan make:policy HolidayPolicy --model=Holiday
php artisan make:policy DepartmentPolicy --model=Department
php artisan make:policy OvertimePolicy --model=Overtime
```

Register in AppServiceProvider:
```php
Gate::policy(Holiday::class, HolidayPolicy::class);
Gate::policy(Department::class, DepartmentPolicy::class);
Gate::policy(Overtime::class, OvertimePolicy::class);
```

---

## 📦 ADDITIONAL PACKAGES NEEDED

```bash
# For better charts
composer require filament/spatie-laravel-tags-plugin

# For advanced exports
composer require pxlrbt/filament-excel

# For calendar
composer require saade/filament-fullcalendar

# For PWA
composer require silviolleite/laravelpwa
```

---

## 🚀 DEPLOYMENT CHECKLIST

- [ ] Run all migrations: `php artisan migrate`
- [ ] Seed holidays: `php artisan db:seed --class=HolidaySeeder`
- [ ] Configure email: Set MAIL_* in .env
- [ ] Setup queue worker: `php artisan queue:work`
- [ ] Setup cron: `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`
- [ ] Storage link: `php artisan storage:link`
- [ ] Optimize: `php artisan optimize`
- [ ] Cache policies: `php artisan policy:cache`

---

## 📝 TESTING SCENARIOS

### Notification System
- [ ] Leave submitted → Admin notified
- [ ] Leave approved → Karyawan notified
- [ ] Late check-in → Manager notified
- [ ] Overtime submitted → Admin notified

### Calendar
- [ ] Cannot check-in on holidays
- [ ] Holiday shows in calendar
- [ ] Recurring holidays work next year

### Overtime
- [ ] Auto-detect from late check-out
- [ ] Approval workflow works
- [ ] Calculation correct

### Reporting
- [ ] Export Excel works
- [ ] Charts display correctly
- [ ] Filters work properly

### Audit Log
- [ ] All changes logged
- [ ] Old/new values captured
- [ ] User tracked correctly

---

## 🎓 TRAINING MATERIAL NEEDED

1. Admin Training
   - How to manage holidays
   - How to approve overtime
   - How to generate reports
   - How to use audit logs

2. Karyawan Training
   - How to use PWA (install on phone)
   - How to take photo for attendance
   - How to submit leave/overtime
   - How to check payroll report

---

## 📈 SUCCESS METRICS

- Notification delivery rate > 95%
- Mobile app usage > 70% of total check-ins
- Report generation time < 5 seconds
- Audit log coverage = 100% of critical actions
- Payroll calculation accuracy = 100%

---

## 🔄 NEXT PHASE IDEAS

- Face recognition integration
- Biometric fingerprint support
- Mobile app (React Native / Flutter)
- Real-time dashboard with websockets
- AI-powered fraud detection
- Integration with payroll software (Mekari, Gadjian)
- Geofencing with multiple locations
- Shift scheduler with AI optimization

---

**Implementation Status:** Database & Models Complete ✅
**Next Action:** Create Filament Resources untuk each feature
**Estimated Time:** 4 weeks (Sprint 1-4)
