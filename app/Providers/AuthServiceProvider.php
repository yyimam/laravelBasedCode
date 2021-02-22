<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Role;
// use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Policies;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
        Gate::define('check', function ($user, $policy_name) {
            $current_user = $user;
            $current_user = User::where('id',Auth::user()->id)->first();
            $current_role_id = $current_user->roles()->first()->id;
            $current_permissions = Role::where('id', $current_role_id)->with("permissions")->first()->permissions->pluck("slug");

            foreach($current_permissions as $perm){
                if ($perm == $policy_name) {
                    return true;
                }
            }
            return false;
        });

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                return User::where('api_token', $request->header('Authorization'))->first();
            }
        });
    }
}
