<?php

namespace App\Policies;

use App\Models\Shift;
use App\Models\User;

class ShiftPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && !$user->isSuperAdmin();
    }

    public function view(User $user, Shift $shift): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $shift->organization_id === $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() && !$user->isSuperAdmin();
    }

    public function update(User $user, Shift $shift): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $shift->organization_id === $user->organization_id;
    }

    public function delete(User $user, Shift $shift): bool
    {
        if ($shift->users()->count() > 0) {
            return false;
        }
        
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $shift->organization_id === $user->organization_id;
    }

    public function restore(User $user, Shift $shift): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $shift->organization_id === $user->organization_id;
    }

    public function forceDelete(User $user, Shift $shift): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $shift->organization_id === $user->organization_id;
    }
}
