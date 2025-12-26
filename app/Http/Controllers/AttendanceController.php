<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
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

        if (!$user->shift) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki shift yang ditetapkan.'
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

        // Check if shift is active
        $currentTime = Carbon::now('Asia/Jakarta');
        if (!$user->shift->isActiveAt($currentTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Absen hanya dapat dilakukan pada jam shift Anda ('. $user->shift->name .': '. Carbon::parse($user->shift->start_time)->format('H:i') . ' - ' . Carbon::parse($user->shift->end_time)->format('H:i') . ').'
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

        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'shift_id' => $user->shift_id,
            'attendance_location_id' => $nearestLocation->id,
            'type' => $request->type,
            'attendance_time' => Carbon::now('Asia/Jakarta'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $filePath,
            'device_id' => $request->device_id,
            'device_model' => $request->device_model,
            'device_os' => $request->device_os,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'face_detected' => $request->face_detected ?? false,
            'face_confidence' => $request->face_confidence,
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->type === 'check_in' ? 'Check In' : 'Check Out') . ' berhasil!',
            'data' => [
                'attendance_time' => $attendance->attendance_time->format('d M Y H:i:s'),
                'location' => $nearestLocation->name,
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
}
