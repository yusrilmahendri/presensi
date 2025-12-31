<?php

namespace App\Policies;

use App\Models\AttendanceLocation;
use App\Models\User;

class AttendanceLocationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && !$user->isSuperAdmin();
    }

    public function view(User $user, AttendanceLocation $attendanceLocation): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendanceLocation->organization_id === $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() && !$user->isSuperAdmin();
    }

    public function update(User $user, AttendanceLocation $attendanceLocation): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendanceLocation->organization_id === $user->organization_id;
    }

    public function delete(User $user, AttendanceLocation $attendanceLocation): bool
    {
        if ($attendanceLocation->attendances()->count() > 0) {
            return false;
        }
        
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendanceLocation->organization_id === $user->organization_id;
    }

    public function restore(User $user, AttendanceLocation $attendanceLocation): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendanceLocation->organization_id === $user->organization_id;
    }

    public function forceDelete(User $user, AttendanceLocation $attendanceLocation): bool
    {
        return $user->isAdmin() 
            && !$user->isSuperAdmin()
            && $attendanceLocation->organization_id === $user->organization_id;
    }
}
