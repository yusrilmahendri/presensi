<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && !$user->isSuperAdmin();
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendance->organization_id === $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() && !$user->isSuperAdmin();
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendance->organization_id === $user->organization_id;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendance->organization_id === $user->organization_id;
    }

    public function restore(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendance->organization_id === $user->organization_id;
    }

    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendance->organization_id === $user->organization_id;
    }
}
