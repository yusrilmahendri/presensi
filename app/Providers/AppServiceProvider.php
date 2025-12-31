<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Organization;
use App\Models\Overtime;
use App\Models\Shift;
use App\Models\User;
use App\Observers\AttendanceObserver;
use App\Policies\AttendanceLocationPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\HolidayPolicy;
use App\Policies\LeavePolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\OvertimePolicy;
use App\Policies\ShiftPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Organization::class => OrganizationPolicy::class,
        User::class => UserPolicy::class,
        Shift::class => ShiftPolicy::class,
        AttendanceLocation::class => AttendanceLocationPolicy::class,
        Attendance::class => AttendancePolicy::class,
        Leave::class => LeavePolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register observers
        Attendance::observe(AttendanceObserver::class);
        
        // Register policies
        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Shift::class, ShiftPolicy::class);
        Gate::policy(AttendanceLocation::class, AttendanceLocationPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Leave::class, LeavePolicy::class);
        Gate::policy(Holiday::class, HolidayPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Overtime::class, OvertimePolicy::class);
        
        // Define additional gates for specific actions
        Gate::define('manage-organizations', function (User $user) {
            return $user->isSuperAdmin();
        });
        
        Gate::define('manage-admins', function (User $user) {
            return $user->isSuperAdmin();
        });
        
        Gate::define('manage-employees', function (User $user) {
            return $user->isAdmin() && !$user->isSuperAdmin();
        });
        
        Gate::define('manage-attendance', function (User $user) {
            return $user->isAdmin() && !$user->isSuperAdmin();
        });
        
        Gate::define('approve-leaves', function (User $user) {
            return $user->isAdmin() && !$user->isSuperAdmin();
        });
        
        Gate::define('approve-overtimes', function (User $user) {
            return $user->isAdmin() && !$user->isSuperAdmin();
        });
    }
}

