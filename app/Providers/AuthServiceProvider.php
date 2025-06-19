<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use App\Models\User;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //  General permission check — expects only user
        Gate::define('can-approve-posts', function (User $user) {
            return $user->hasPermission('approve-post');
        });

        Gate::define('approve-post', function (User $user, Post $post) {
            return $user->hasPermission('approve-post') && $post->user_id !==  $user->id;
        });
    }

}
