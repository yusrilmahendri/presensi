<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        // Ensure user is karyawan
        if (Auth::user()->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        $locations = AttendanceLocation::where('organization_id', $user->organization_id)->get();
        return view('attendance.index', compact('locations', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string', // base64 image
            'type' => 'required|in:check_in,check_out',
            'device_id' => 'nullable|string',
            'device_model' => 'nullable|string',
            'device_os' => 'nullable|string',
            'face_detected' => 'nullable|boolean',
            'face_confidence' => 'nullable|integer',
        ]);

        // Get authenticated user
        $user = Auth::user();

        // Ensure user is karyawan
        if ($user->role !== 'karyawan') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }
        
        // === SECURITY CHECK: Face Detection ===
        if (!$request->face_detected) {
            // Log suspicious attempt
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'organization_id' => $user->organization_id,
                'event' => 'no_face_detected',
                'description' => 'Attendance attempt without face detection - possible proxy attendance',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '⚠️ WAJAH TIDAK TERDETEKSI!\n\n' .
                            'Sistem tidak mendeteksi wajah dalam foto selfie Anda.\n\n' .
                            'Kemungkinan penyebab:\n' .
                            '• Anda menggunakan foto orang lain\n' .
                            '• Pencahayaan terlalu gelap\n' .
                            '• Wajah tertutup/tidak terlihat jelas\n\n' .
                            '⚠️ PERINGATAN: Insiden ini telah dicatat dalam sistem audit.\n\n' .
                            'Silakan ambil foto ulang dengan:\n' .
                            '✓ Wajah terlihat jelas\n' .
                            '✓ Pencahayaan cukup\n' .
                            '✓ Tidak tertutup masker/topi'
            ], 400);
        }
        
        if ($request->face_confidence && $request->face_confidence < 60) {
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'organization_id' => $user->organization_id,
                'event' => 'low_face_confidence',
                'description' => 'Low face detection confidence: ' . $request->face_confidence . '% - possible fake photo',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '⚠️ KUALITAS WAJAH RENDAH!\n\n' .
                            'Tingkat keyakinan pendeteksian wajah: ' . $request->face_confidence . '%\n' .
                            '(Minimum diperlukan: 60%)\n\n' .
                            'Kemungkinan penyebab:\n' .
                            '• Pencahayaan buruk\n' .
                            '• Wajah tidak jelas (blur/jauh)\n' .
                            '• Menggunakan foto printout/layar HP\n\n' .
                            '⚠️ PERINGATAN: Insiden dicatat dalam sistem audit.\n\n' .
                            'Ambil foto ulang dengan kondisi lebih baik!'
            ], 400);
        }
        
        // === SECURITY CHECK: Device Fingerprinting ===
        if ($request->device_id) {
            $lastAttendance = Attendance::where('user_id', $user->id)
                ->whereNotNull('device_id')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($lastAttendance && $lastAttendance->device_id !== $request->device_id) {
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'organization_id' => $user->organization_id,
                    'event' => 'device_change_detected',
                    'description' => 'Attendance from different device. Old: ' . $lastAttendance->device_id . ', New: ' . $request->device_id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                
                // Not blocking, just logging for admin review
            }
        }

        // Validasi shift hanya untuk karyawan dengan work_type = 'shift'
        if ($user->work_type === 'shift' && !$user->shift) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki shift yang ditetapkan.'
            ], 400);
        }

        // Check if user is on approved leave today
        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        $hasLeaveToday = \App\Models\Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();
        
        if ($hasLeaveToday) {
            return response()->json([
                'success' => false,
                'message' => '❌ Anda tidak dapat melakukan absensi karena sedang dalam masa cuti/izin yang telah disetujui. Silakan hubungi admin jika ada kesalahan.'
            ], 400);
        }

        // Find nearest location within organization
        $locations = AttendanceLocation::where('organization_id', $user->organization_id)->get();
        
        if ($locations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada lokasi absen yang dikonfigurasi untuk organisasi Anda.'
            ], 400);
        }
        
        $nearestLocation = null;
        $minDistance = PHP_FLOAT_MAX;
        $closestLocation = null;
        $closestDistance = PHP_FLOAT_MAX;

        foreach ($locations as $location) {
            $distance = $location->distanceInMeters($request->latitude, $request->longitude);
            
            // Track closest location for error message
            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestLocation = $location;
            }
            
            // Check if within radius (strict - no tolerance)
            if ($distance <= $location->radius && $distance < $minDistance) {
                $minDistance = $distance;
                $nearestLocation = $location;
            }
        }

        if (!$nearestLocation) {
            $selisih = round($closestDistance - $closestLocation->radius);
            
            return response()->json([
                'success' => false,
                'message' => '🚫 Anda berada di luar radius lokasi absen! ' .
                            '📍 Lokasi: ' . $closestLocation->name . ' | ' .
                            '🎯 Radius: ' . $closestLocation->radius . 'm | ' .
                            '📏 Jarak Anda: ' . round($closestDistance) . 'm | ' .
                            '⚠️ Kurang: ' . $selisih . 'm lagi. ' .
                            '💡 Solusi: Berjalanlah lebih dekat ke titik lokasi atau hubungi admin untuk verifikasi koordinat.'
            ], 400);
        }

        // Check if already checked in/out today for this type
        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('type', $request->type)
            ->whereDate('attendance_time', $today)
            ->first();

        if ($existingAttendance) {
            $typeName = $request->type === 'check_in' ? 'check in' : 'check out';
            return response()->json([
                'success' => false,
                'message' => "Anda sudah melakukan {$typeName} hari ini."
            ], 400);
        }

        // For check_out, ensure user has checked in today
        if ($request->type === 'check_out') {
            $checkInToday = Attendance::where('user_id', $user->id)
                ->where('type', 'check_in')
                ->whereDate('attendance_time', $today)
                ->first();

            if (!$checkInToday) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan check in hari ini.'
                ], 400);
            }

            // Mode Working Hours: Validasi jam kerja minimum dan deteksi overtime
            if ($user->work_type === 'working_hours') {
                $checkInTime = Carbon::parse($checkInToday->attendance_time);
                $currentTime = Carbon::now('Asia/Jakarta');
                $hoursWorked = $checkInTime->diffInHours($currentTime, true);
                $organization = $user->organization;
                
                if (!$organization) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Organisasi tidak ditemukan. Silakan hubungi admin.'
                    ], 400);
                }
                
                $minHours = $organization->min_working_hours ?? 8;
                $gracePeriod = $organization->grace_period ?? 1;
                $maxHoursBeforeOvertime = $minHours + $gracePeriod;

                // Validasi minimum jam kerja
                if ($hoursWorked < $minHours) {
                    $remainingHours = $minHours - $hoursWorked;
                    $remainingMinutes = round(($remainingHours - floor($remainingHours)) * 60);
                    
                    return response()->json([
                        'success' => false,
                        'message' => "⏰ Belum mencapai jam kerja minimum!\n\n" .
                                    "📋 Jam kerja minimum: {$minHours} jam\n" .
                                    "⏱️ Anda telah bekerja: " . floor($hoursWorked) . " jam " . round(($hoursWorked - floor($hoursWorked)) * 60) . " menit\n" .
                                    "⚠️ Kurang: " . floor($remainingHours) . " jam " . $remainingMinutes . " menit lagi\n\n" .
                                    "💡 Silakan tunggu hingga mencapai jam kerja minimum."
                    ], 400);
                }

                // Deteksi overtime - set flag but continue to create attendance
                $overtimeDetected = false;
                if ($hoursWorked > $maxHoursBeforeOvertime) {
                    $overtimeHours = $hoursWorked - $maxHoursBeforeOvertime;
                    $overtimeMinutes = round($overtimeHours * 60);
                    $overtimeDetected = true;
                    
                    // Simpan data ke session untuk auto-fill di modal overtime
                    session([
                        'overtime_auto_fill' => [
                            'date' => $checkInTime->format('Y-m-d'),
                            'start_time' => $checkInTime->format('H:i'),
                            'end_time' => $currentTime->format('H:i'),
                            'hours_worked' => round($hoursWorked, 2),
                            'max_hours' => $maxHoursBeforeOvertime,
                            'overtime_hours' => round($overtimeHours, 2),
                            'overtime_minutes' => $overtimeMinutes,
                        ]
                    ]);
                }
            }
            
            // Mode Shift: Deteksi overtime berdasarkan shift
            $shiftOvertimeDetected = false;
            if ($user->work_type === 'shift' && $user->shift) {
                $checkInTime = Carbon::parse($checkInToday->attendance_time);
                $currentTime = Carbon::now('Asia/Jakarta');
                $shiftEnd = Carbon::parse($user->shift->end_time);
                $shiftEnd->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                
                // Jika checkout setelah shift end + grace period
                $organization = $user->organization;
                $gracePeriod = $organization ? ($organization->grace_period ?? 1) : 1;
                $maxEndTime = $shiftEnd->copy()->addHours($gracePeriod);
                
                if ($currentTime->greaterThan($maxEndTime)) {
                    $overtimeMinutes = $currentTime->diffInMinutes($shiftEnd);
                    $overtimeHours = $overtimeMinutes / 60;
                    $shiftOvertimeDetected = true;
                    
                    // Simpan data ke session untuk auto-fill di modal overtime
                    session([
                        'overtime_auto_fill' => [
                            'date' => $checkInTime->format('Y-m-d'),
                            'start_time' => $shiftEnd->format('H:i'),
                            'end_time' => $currentTime->format('H:i'),
                            'shift_end' => $shiftEnd->format('H:i'),
                            'check_out_time' => $currentTime->format('H:i'),
                            'overtime_hours' => round($overtimeHours, 2),
                            'overtime_minutes' => $overtimeMinutes,
                        ]
                    ]);
                }
            }
            
            // Mode Shift: Deteksi overtime berdasarkan shift
            $shiftOvertimeDetected = false;
            if ($user->work_type === 'shift' && $user->shift) {
                $checkInTime = Carbon::parse($checkInToday->attendance_time);
                $currentTime = Carbon::now('Asia/Jakarta');
                $shiftEnd = Carbon::parse($user->shift->end_time);
                $shiftEnd->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                
                // Jika checkout setelah shift end + grace period
                $organization = $user->organization;
                $gracePeriod = $organization ? ($organization->grace_period ?? 1) : 1;
                $maxEndTime = $shiftEnd->copy()->addHours($gracePeriod);
                
                if ($currentTime->greaterThan($maxEndTime)) {
                    $overtimeMinutes = $currentTime->diffInMinutes($shiftEnd);
                    $overtimeHours = $overtimeMinutes / 60;
                    $shiftOvertimeDetected = true;
                    
                    // Simpan data ke session untuk auto-fill di modal overtime
                    session([
                        'overtime_auto_fill' => [
                            'date' => $checkInTime->format('Y-m-d'),
                            'start_time' => $shiftEnd->format('H:i'),
                            'end_time' => $currentTime->format('H:i'),
                            'shift_end' => $shiftEnd->format('H:i'),
                            'check_out_time' => $currentTime->format('H:i'),
                            'overtime_hours' => round($overtimeHours, 2),
                            'overtime_minutes' => $overtimeMinutes,
                        ]
                    ]);
                }
            }
        }

        // Save photo
        $photoData = $request->photo;
        $photoData = str_replace('data:image/png;base64,', '', $photoData);
        $photoData = str_replace('data:image/jpeg;base64,', '', $photoData);
        $photoData = str_replace(' ', '+', $photoData);
        $image = base64_decode($photoData);
        
        $fileName = 'attendance_' . $user->id . '_' . time() . '.jpg';
        $filePath = 'attendances/' . $fileName;
        Storage::disk('public')->put($filePath, $image);

        // Determine attendance status for check-in
        $status = null;
        $organization = $user->organization;
        
        if ($request->type === 'check_in') {
            // Cek work_type karyawan, bukan organization mode
            if ($user->work_type === 'working_hours') {
                // Mode Working Hours: Karyawan bebas check-in kapan saja
                $status = 'flexible';
            } 
            // Mode Shift: Validasi berdasarkan jam shift
            elseif ($user->work_type === 'shift' && $user->shift) {
                $currentTime = Carbon::now('Asia/Jakarta');
                $shiftStart = Carbon::parse($user->shift->start_time);
                $shiftStart->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                
                // Calculate difference in minutes (negative = early, positive = late)
                $diffMinutes = $currentTime->diffInMinutes($shiftStart, false);
                
                if ($diffMinutes < -15) {
                    // More than 15 minutes before shift start
                    $status = 'early';
                } elseif ($diffMinutes <= 0) {
                    // Within 15 minutes before shift or exactly on time
                    $status = 'on_time';
                } else {
                    // After shift start time
                    $status = 'late';
                }
            }
        }

        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'shift_id' => $user->shift_id,
            'attendance_location_id' => $nearestLocation->id,
            'type' => $request->type,
            'status' => $status,
            'attendance_time' => Carbon::now('Asia/Jakarta'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $filePath,
        ]);
        
        // Add status message for check-in
        $statusMessage = '';
        if ($request->type === 'check_in' && $status) {
            if ($status === 'late') {
                $statusMessage = ' Anda terlambat.';
            } elseif ($status === 'early') {
                $statusMessage = ' Anda lebih awal.';
            } else {
                $statusMessage = ' Tepat waktu!';
            }
        }

        // Check if overtime was detected during check-out
        if ($request->type === 'check_out' && (isset($overtimeDetected) && $overtimeDetected || isset($shiftOvertimeDetected) && $shiftOvertimeDetected)) {
            return response()->json([
                'success' => true,
                'overtime_detected' => true,
                'message' => 'Check Out berhasil! Anda melebihi jam kerja. Silakan ajukan lembur.',
                'redirect_url' => route('karyawan.overtime.index'),
                'data' => [
                    'attendance_time' => $attendance->attendance_time->format('d M Y H:i:s'),
                    'location' => $nearestLocation->name,
                    'status' => $status,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->type === 'check_in' ? 'Check In' : 'Check Out') . ' berhasil!' . $statusMessage,
            'data' => [
                'attendance_time' => $attendance->attendance_time->format('d M Y H:i:s'),
                'location' => $nearestLocation->name,
                'status' => $status,
            ]
        ]);
    }
    
    public function logFakeGps(Request $request)
    {
        $user = Auth::user();
        
        // Log to audit log or separate fake GPS attempts table
        \Log::warning('Fake GPS attempt detected', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'email' => $user->email,
            'organization_id' => $user->organization_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'altitude' => $request->altitude,
            'altitudeAccuracy' => $request->altitudeAccuracy,
            'heading' => $request->heading,
            'speed' => $request->speed,
            'timestamp' => $request->timestamp,
            'reasons' => $request->reasons,
            'user_agent' => $request->user_agent,
            'ip_address' => $request->ip(),
            'detected_at' => Carbon::now('Asia/Jakarta')
        ]);
        
        // You can also create an AuditLog entry
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'event' => 'fake_gps_detected',
            'description' => 'Fake GPS attempt detected. Reasons: ' . implode(', ', $request->reasons ?? []),
            'ip_address' => $request->ip(),
            'user_agent' => $request->user_agent,
        ]);
        
        return response()->json(['success' => true]);
    }

    public function submitOvertime(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'reason' => 'required|string|min:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string',
        ]);

        $user = Auth::user();
        $attendance = Attendance::where('id', $request->attendance_id)
            ->where('user_id', $user->id)
            ->where('type', 'check_in')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance tidak ditemukan.'
            ], 404);
        }

        // Save checkout photo
        $photoData = $request->photo;
        $photoData = str_replace('data:image/png;base64,', '', $photoData);
        $photoData = str_replace('data:image/jpeg;base64,', '', $photoData);
        $photoData = str_replace(' ', '+', $photoData);
        $image = base64_decode($photoData);
        
        $fileName = 'attendance_' . $user->id . '_' . time() . '.jpg';
        $filePath = 'attendances/' . $fileName;
        Storage::disk('public')->put($filePath, $image);

        // Get nearest location
        $nearestLocation = AttendanceLocation::selectRaw(
            'id, name, latitude, longitude, radius,
            (6371000 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
            [$request->latitude, $request->longitude, $request->latitude]
        )
        ->where('organization_id', $user->organization_id)
        ->orderBy('distance', 'asc')
        ->first();

        if (!$nearestLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada lokasi yang tersedia.'
            ], 400);
        }

        if ($nearestLocation->distance > $nearestLocation->radius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar jangkauan lokasi absensi. Jarak Anda: ' . round($nearestLocation->distance) . ' meter.'
            ], 400);
        }

        // Create checkout attendance
        $checkOutAttendance = Attendance::create([
            'user_id' => $user->id,
            'shift_id' => $user->shift_id,
            'attendance_location_id' => $nearestLocation->id,
            'type' => 'check_out',
            'status' => null,
            'attendance_time' => Carbon::now('Asia/Jakarta'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $filePath,
        ]);

        // Calculate overtime duration
        $checkInTime = Carbon::parse($attendance->attendance_time);
        $checkOutTime = Carbon::parse($checkOutAttendance->attendance_time);
        $organization = $user->organization;

        $overtimeDuration = 0;

        if ($user->work_type === 'working_hours') {
            $minHours = $organization ? $organization->min_working_hours : 8;
            $gracePeriod = $organization ? $organization->grace_period : 1;
            $maxHoursBeforeOvertime = $minHours + $gracePeriod;
            
            $hoursWorked = $checkInTime->diffInHours($checkOutTime, true);
            if ($hoursWorked > $maxHoursBeforeOvertime) {
                $overtimeDuration = ($hoursWorked - $maxHoursBeforeOvertime) * 60; // in minutes
            }
        } elseif ($user->work_type === 'shift' && $user->shift) {
            $shiftEnd = Carbon::parse($user->shift->end_time);
            $shiftEnd->setDate($checkOutTime->year, $checkOutTime->month, $checkOutTime->day);
            
            $gracePeriod = $organization ? $organization->grace_period : 1;
            $maxEndTime = $shiftEnd->copy()->addHours($gracePeriod);
            
            if ($checkOutTime->greaterThan($maxEndTime)) {
                $overtimeDuration = $checkOutTime->diffInMinutes($shiftEnd);
            }
        }

        // Create overtime request
        $overtime = Overtime::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'date' => $checkInTime->toDateString(),
            'start_time' => $checkInTime->toTimeString(),
            'end_time' => $checkOutTime->toTimeString(),
            'duration_minutes' => round($overtimeDuration),
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check Out berhasil! Pengajuan lembur telah dikirim untuk persetujuan.',
            'data' => [
                'attendance_time' => $checkOutAttendance->attendance_time->format('d M Y H:i:s'),
                'location' => $nearestLocation->name,
                'overtime_duration' => round($overtimeDuration),
                'overtime_status' => 'pending',
            ]
        ]);
    }
}
