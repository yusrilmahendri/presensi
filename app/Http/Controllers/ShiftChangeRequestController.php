<?php

namespace App\Http\Controllers;

use App\Models\ShiftChangeRequest;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftChangeRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }
        
        $requests = ShiftChangeRequest::where('user_id', $user->id)
            ->with(['currentShift', 'requestedShift', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $availableShifts = Shift::where('organization_id', $user->organization_id)
            ->where('id', '!=', $user->shift_id)
            ->get();
        
        return view('karyawan.shift-change', compact('requests', 'availableShifts', 'user'));
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'karyawan') {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'requested_shift_id' => 'required|exists:shifts,id',
            'effective_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|min:10',
        ]);
        
        // Check if there's already a pending request
        $existingPending = ShiftChangeRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
        
        if ($existingPending) {
            return back()->with('error', 'Anda masih memiliki pengajuan yang belum diproses.');
        }
        
        ShiftChangeRequest::create([
            'user_id' => $user->id,
            'current_shift_id' => $user->shift_id,
            'requested_shift_id' => $request->requested_shift_id,
            'effective_date' => $request->effective_date,
            'reason' => $request->reason,
            'status' => 'pending',
            'organization_id' => $user->organization_id,
        ]);
        
        return back()->with('success', 'Pengajuan pergantian shift berhasil diajukan!');
    }
    
    public function cancel($id)
    {
        $user = Auth::user();
        
        $request = ShiftChangeRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        $request->delete();
        
        return back()->with('success', 'Pengajuan pergantian shift berhasil dibatalkan.');
    }
}
