<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\TodoPolicy;
use App\Models\Todo;
use Laravel\Passport\Passport;  //import Passport here

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
        //cannot use gates and policies at once
      //  Todo::class => TodoPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        /* define a admin user role */



        Gate::define('isAdmin', function ($user) {
            return $user->role == 'admin';
        });

        /* define a manager user role */

        Gate::define('isManager', function ($user) {
            return $user->role == 'manager';
        });

        /* define a user role */

        Gate::define('isUser', function ($user) {
            return $user->role == 'user';
        });


        //crud gates

        Gate::define('can_get', function ($user, $todo) {
            return $user->id == $todo->user_id;
        });

        Gate::define('can_delete', function ($user, $todo) {
            return $user->id == $todo->user_id;
        });
    }
}
