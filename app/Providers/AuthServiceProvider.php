<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Should return TRUE or FALSE if system admin
        Gate::define('manage_system', function(User $user) {
            return $user->access_type === "System Admin";
        });

        // Should return TRUE or FALSE if General administration
        Gate::define('general_admin', function(User $user) {
            return $user->access_type === "General Admin";
        });

        // Should return TRUE or FALSE if Registrar
        Gate::define('developer_user', function(User $user) {
            return $user->access_type === "Developer";
        });

        // Should return TRUE or FALSE if Registrar
        Gate::define('management_admin', function(User $user) {
            return $user->access_type === "Management";
        });

        // Should return TRUE or FALSE if Registrar
        Gate::define('court_registrar', function(User $user) {
            return $user->access_type === "Director";
        });

        // Should return TRUE or FALSE if Court Staff
        Gate::define('court_staff', function(User $user) {
            return $user->access_type === "Staff";
        });


    }
}
