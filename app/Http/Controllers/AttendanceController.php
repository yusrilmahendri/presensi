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
        $locations = AttendanceLocation::all();
        return view('attendance.index', compact('locations', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string', // base64 image
            'type' => 'required|in:check_in,check_out',
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

        if (!$user->shift) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki shift yang ditetapkan.'
            ], 400);
        }

        // Find nearest location
        $locations = AttendanceLocation::all();
        $nearestLocation = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($locations as $location) {
            $distance = $location->distanceInMeters($request->latitude, $request->longitude);
            if ($distance <= $location->radius && $distance < $minDistance) {
                $minDistance = $distance;
                $nearestLocation = $location;
            }
        }

        if (!$nearestLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius lokasi absen yang ditentukan (5 meter).'
            ], 400);
        }

        // Check if shift is active
        $currentTime = Carbon::now()->format('H:i:s');
        if (!$user->shift->isActiveAt($currentTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Absen hanya dapat dilakukan pada jam shift Anda ('. $user->shift->name .': '. Carbon::parse($user->shift->start_time)->format('H:i') . ' - ' . Carbon::parse($user->shift->end_time)->format('H:i') . ').'
            ], 400);
        }

        // Check if already checked in/out today for this type
        $today = Carbon::today();
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
            'attendance_time' => Carbon::now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $filePath,
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
}

