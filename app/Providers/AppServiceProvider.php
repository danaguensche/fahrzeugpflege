<?php

namespace App\Providers;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;


class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router)
    {
        Validator::resolver(function($translator, $data, $rules, $messages) {
            return new class($translator, $data, $rules, $messages) extends \Illuminate\Validation\Validator {
            };
        });

        $authLimiter = function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        };

        RateLimiter::for('login', $authLimiter);
        RateLimiter::for('signup', $authLimiter);

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
    
}

