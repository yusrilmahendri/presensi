<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function index()
    {
        // Ensure user is karyawan
        if (Auth::user()->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        $overtimes = Overtime::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('karyawan.overtime.index', compact('overtimes'));
    }

    public function store(Request $request)
    {
        // Ensure user is karyawan
        if (Auth::user()->role !== 'karyawan') {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        
        // Calculate duration in minutes
        $startTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endTime = Carbon::parse($request->date . ' ' . $request->end_time);
        $durationMinutes = $startTime->diffInMinutes($endTime);

        // Determine multiplier based on time (weekday = 1.5x, weekend = 2x)
        $date = Carbon::parse($request->date);
        $multiplier = $date->isWeekend() ? 2.0 : 1.5;

        Overtime::create([
            'organization_id' => $user->organization_id,
            'user_id' => $user->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $durationMinutes,
            'multiplier' => $multiplier,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan lembur berhasil dibuat. Menunggu persetujuan admin.');
    }

    public function cancel($id)
    {
        $overtime = Overtime::findOrFail($id);

        // Ensure user owns this overtime request
        if ($overtime->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        // Only allow canceling pending requests
        if ($overtime->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan yang masih pending yang bisa dibatalkan.');
        }

        $overtime->delete();

        return back()->with('success', 'Pengajuan lembur berhasil dibatalkan.');
    }
}
