<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'email',
        'phone',
        'address',
        'logo',
        'is_active',
        'max_users',
        'enabled_attendance_modes',
        'min_working_hours',
        'grace_period_hours',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'enabled_attendance_modes' => 'array',
        'min_working_hours' => 'integer',
        'grace_period_hours' => 'integer',
    ];

    /**
     * Get all users for the organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all shifts for the organization.
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    /**
     * Get all attendance locations for the organization.
     */
    public function attendanceLocations(): HasMany
    {
        return $this->hasMany(AttendanceLocation::class);
    }

    /**
     * Get all attendances for the organization.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all leaves for the organization.
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Get admins for the organization.
     */
    public function admins(): HasMany
    {
        return $this->users()->where('role', 'admin');
    }

    /**
     * Get karyawan for the organization.
     */
    public function karyawan(): HasMany
    {
        return $this->users()->where('role', 'karyawan');
    }

    /**
     * Get active users count.
     */
    public function activeUsersCount(): int
    {
        return $this->users()->count();
    }

    /**
     * Check if organization can add more users.
     */
    public function canAddUsers(): bool
    {
        return $this->activeUsersCount() < $this->max_users;
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'umkm' => 'UMKM',
            'instansi' => 'Instansi',
            'perusahaan' => 'Perusahaan',
            'lainnya' => 'Lainnya',
            default => $this->type,
        };
    }

    /**
     * Check if organization has shift mode enabled.
     */
    public function isShiftBased(): bool
    {
        $modes = $this->enabled_attendance_modes ?? ['shift'];
        return in_array('shift', $modes);
    }

    /**
     * Check if organization has working hours mode enabled.
     */
    public function isWorkingHoursBased(): bool
    {
        $modes = $this->enabled_attendance_modes ?? ['shift'];
        return in_array('working_hours', $modes);
    }

    /**
     * Get enabled attendance modes.
     */
    public function getEnabledModes(): array
    {
        return $this->enabled_attendance_modes ?? ['shift'];
    }

    /**
     * Get maximum work hours before overtime (minimum + grace period).
     */
    public function getMaxWorkHoursBeforeOvertime(): int
    {
        return $this->min_working_hours + $this->grace_period_hours;
    }
}
