<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceLocation extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius',
        'description',
        'organization_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'integer',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Calculate distance between two coordinates in meters using Haversine formula
     */
    public function distanceInMeters(float $latitude, float $longitude): float
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($latitude - $this->latitude);
        $dLon = deg2rad($longitude - $this->longitude);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function isWithinRadius(float $latitude, float $longitude): bool
    {
        $distance = $this->distanceInMeters($latitude, $longitude);
        return $distance <= $this->radius;
    }
}

