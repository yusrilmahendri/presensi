<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftChangeRequest extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'user_id',
        'current_shift_id',
        'requested_shift_id',
        'effective_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'organization_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'effective_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentShift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'current_shift_id');
    }

    public function requestedShift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'requested_shift_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
