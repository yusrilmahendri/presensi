<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;

class LeavePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && !$user->isSuperAdmin();
    }

    public function view(User $user, Leave $leave): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $leave->organization_id === $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() && !$user->isSuperAdmin();
    }

    public function update(User $user, Leave $leave): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $leave->organization_id === $user->organization_id;
    }

    public function delete(User $user, Leave $leave): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $leave->organization_id === $user->organization_id;
    }

    public function approve(User $user, Leave $leave): bool
    {
        // Admin can approve/reject leave in their organization
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $leave->organization_id === $user->organization_id
            && $leave->status === 'pending';
    }

    public function restore(User $user, Leave $leave): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $leave->organization_id === $user->organization_id;
    }

    public function forceDelete(User $user, Leave $leave): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $leave->organization_id === $user->organization_id;
    }
}
