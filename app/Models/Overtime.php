<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Overtime extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'user_id',
        'attendance_id',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'multiplier',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'multiplier' => 'decimal:2',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }

    public function calculateDuration()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        return $start->diffInMinutes($end);
    }
}
