<?php

namespace App\Providers;

use App\Models\PersonalAccessToken as ModelsPersonalAccessToken;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;


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

        //
        Sanctum::usePersonalAccessTokenModel(ModelsPersonalAccessToken::class);

    }
}
