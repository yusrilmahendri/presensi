<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super admin can view all admins, regular admin can view their employees
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Super admin can view any admin
        if ($user->isSuperAdmin()) {
            return $model->role === 'admin';
        }
        
        // Regular admin can only view users in their organization
        if ($user->isAdmin()) {
            return $model->organization_id === $user->organization_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super admin can create admin accounts
        // Regular admin can create employee accounts
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Super admin can update any admin (but not super admin)
        if ($user->isSuperAdmin()) {
            return $model->role === 'admin' && !$model->isSuperAdmin();
        }
        
        // Regular admin can only update users in their organization (not themselves)
        if ($user->isAdmin()) {
            return $model->organization_id === $user->organization_id 
                && $model->id !== $user->id
                && $model->role !== 'super_admin';
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }
        
        // Cannot delete super admin
        if ($model->isSuperAdmin()) {
            return false;
        }
        
        // Super admin can delete admin accounts
        if ($user->isSuperAdmin()) {
            return $model->role === 'admin';
        }
        
        // Regular admin can delete employees in their organization
        if ($user->isAdmin()) {
            return $model->organization_id === $user->organization_id 
                && $model->role === 'karyawan';
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Super admin can restore admin accounts
        if ($user->isSuperAdmin()) {
            return $model->role === 'admin';
        }
        
        // Regular admin can restore employees in their organization
        if ($user->isAdmin()) {
            return $model->organization_id === $user->organization_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only super admin can force delete
        // Cannot force delete super admin or yourself
        return $user->isSuperAdmin() 
            && !$model->isSuperAdmin() 
            && $user->id !== $model->id;
    }
}
