<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        $leaves = Leave::where('user_id', $user->id)
            ->with(['approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('karyawan.leaves.index', compact('leaves', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'type' => 'required|in:sakit,izin,cuti',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_days' => 'required|integer|min:1',
            'reason' => 'required|string|max:65535',
            'attachment' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['user_id'] = $user->id;
        $data['status'] = 'pending';

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = 'leave_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('leaves', $fileName, 'public');
            $data['attachment'] = $filePath;
        }

        Leave::create($data);

        return redirect()->route('karyawan.leaves.index')
            ->with('success', 'Pengajuan izin berhasil dikirim dan menunggu persetujuan admin.');
    }
}
