<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'name',
        'date',
        'type',
        'description',
        'is_recurring',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public static function isHoliday($date, $organizationId = null): bool
    {
        $query = self::where('date', $date)->where('is_active', true);

        if ($organizationId) {
            $query->where(function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId)->orWhere('type', 'national');
            });
        } else {
            $query->where('type', 'national');
        }

        return $query->exists();
    }

    public static function getHolidaysInRange($startDate, $endDate, $organizationId = null)
    {
        $query = self::whereBetween('date', [$startDate, $endDate])->where('is_active', true);

        if ($organizationId) {
            $query->where(function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId)->orWhere('type', 'national');
            });
        }

        return $query->orderBy('date')->get();
    }
}
