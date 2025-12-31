<?php

namespace App\Policies;

use App\Models\Overtime;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OvertimePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'karyawan']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Overtime $overtime): bool
    {
        // Admin can view all in their organization
        if ($user->role === 'admin') {
            return $user->organization_id === $overtime->organization_id;
        }
        
        // Karyawan can only view their own
        return $user->id === $overtime->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'karyawan';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Overtime $overtime): bool
    {
        // Only allow update if still pending
        if ($overtime->status !== 'pending') {
            return false;
        }
        
        // Karyawan can edit their own pending overtime
        return $user->id === $overtime->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Overtime $overtime): bool
    {
        // Only allow delete if still pending
        if ($overtime->status !== 'pending') {
            return false;
        }
        
        // Admin can delete any pending overtime in their organization
        if ($user->role === 'admin') {
            return $user->organization_id === $overtime->organization_id;
        }
        
        // Karyawan can delete their own pending overtime
        return $user->id === $overtime->user_id;
    }

    /**
     * Determine whether the user can approve overtime.
     */
    public function approve(User $user, Overtime $overtime): bool
    {
        return $user->role === 'admin' 
            && $user->organization_id === $overtime->organization_id
            && $overtime->status === 'pending';
    }

    /**
     * Determine whether the user can reject overtime.
     */
    public function reject(User $user, Overtime $overtime): bool
    {
        return $user->role === 'admin' 
            && $user->organization_id === $overtime->organization_id
            && $overtime->status === 'pending';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Overtime $overtime): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Overtime $overtime): bool
    {
        return false;
    }
}
