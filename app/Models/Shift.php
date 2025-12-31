<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'description',
        'organization_id',
    ];

    // No casts needed for TIME columns - they are stored as strings

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function isActiveAt($time = null): bool
    {
        $currentTime = $time ? \Carbon\Carbon::parse($time) : \Carbon\Carbon::now();
        $currentHour = (int) $currentTime->format('H');
        $currentMinute = (int) $currentTime->format('i');
        $currentTotalMinutes = $currentHour * 60 + $currentMinute;

        // Parse time string (format: HH:MM:SS or HH:MM)
        $startParts = explode(':', $this->start_time);
        $startTotalMinutes = ((int) $startParts[0]) * 60 + ((int) $startParts[1]);

        $endParts = explode(':', $this->end_time);
        $endTotalMinutes = ((int) $endParts[0]) * 60 + ((int) $endParts[1]);

        // Handle shift that spans midnight (e.g., 23:00 to 07:00)
        if ($startTotalMinutes > $endTotalMinutes) {
            // Shift spans midnight
            return $currentTotalMinutes >= $startTotalMinutes || $currentTotalMinutes < $endTotalMinutes;
        }

        // Normal shift (same day)
        return $currentTotalMinutes >= $startTotalMinutes && $currentTotalMinutes < $endTotalMinutes;
    }
}

