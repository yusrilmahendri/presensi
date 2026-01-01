<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Exports\AttendancesExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ensure only karyawan can access
        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        // Get today's attendance
        $today = Carbon::today();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_time', $today)
            ->orderBy('attendance_time', 'desc')
            ->get();

        $checkInToday = $todayAttendance->where('type', 'check_in')->first();
        $checkOutToday = $todayAttendance->where('type', 'check_out')->first();

        // Get recent attendances (last 30 days)
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->with('attendanceLocation')
            ->orderBy('attendance_time', 'desc')
            ->limit(30)
            ->get();

        // Statistics
        $thisMonth = Carbon::now()->startOfMonth();
        $thisMonthAttendance = Attendance::where('user_id', $user->id)
            ->where('attendance_time', '>=', $thisMonth)
            ->count();

        $thisWeek = Carbon::now()->startOfWeek();
        $thisWeekAttendance = Attendance::where('user_id', $user->id)
            ->where('attendance_time', '>=', $thisWeek)
            ->count();

        // Dynamic greeting based on time (Jakarta timezone)
        $hour = Carbon::now('Asia/Jakarta')->hour;
        if ($hour >= 0 && $hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        return view('karyawan.dashboard', compact(
            'user',
            'checkInToday',
            'checkOutToday',
            'recentAttendances',
            'thisMonthAttendance',
            'thisWeekAttendance',
            'greeting'
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        $fileName = 'riwayat-absensi-' . $user->name . '-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return Excel::download(
            new AttendancesExport(
                $request->start_date,
                $request->end_date,
                $user->id,
                null
            ),
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        $query = Attendance::with(['user', 'shift', 'attendanceLocation'])
            ->where('user_id', $user->id)
            ->orderBy('attendance_time', 'desc');

        if ($request->start_date) {
            $query->whereDate('attendance_time', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('attendance_time', '<=', $request->end_date);
        }

        $attendances = $query->get();

        $pdf = Pdf::loadView('exports.attendances-pdf', [
            'attendances' => $attendances,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
        ])->setPaper('a4', 'landscape');

        $fileName = 'riwayat-absensi-' . $user->name . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($fileName);
    }

    public function profile()
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        return view('karyawan.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updated = false;

        if ($request->filled('username') && $request->username !== $user->username) {
            $user->username = $request->username;
            $updated = true;
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $updated = true;
        }

        if ($updated) {
            $user->save();
            return redirect()->route('karyawan.profile')->with('success', 'Profile berhasil diperbarui!');
        }

        return redirect()->route('karyawan.profile')->with('info', 'Tidak ada perubahan data.');
    }
}
