<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToOrganization(): void
    {
        // Automatically scope queries by organization for non-super-admin users
        static::addGlobalScope('organization', function (Builder $builder) {
            $user = auth()->user();
            
            // Skip scope for super admin or if no user is authenticated
            if (!$user || ($user->role ?? null) === 'super_admin') {
                return;
            }

            // Apply organization scope for admin and karyawan
            if ($user->organization_id) {
                $builder->where($builder->getModel()->getTable() . '.organization_id', $user->organization_id);
            }
        });

        // Automatically set organization_id when creating
        static::creating(function ($model) {
            $user = auth()->user();
            
            if ($user && !$model->organization_id && $user->organization_id) {
                $model->organization_id = $user->organization_id;
            }
        });
    }

    /**
     * Get the organization that owns the model.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
