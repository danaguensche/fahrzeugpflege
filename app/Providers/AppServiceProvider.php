<?php

namespace App\Providers;
use App\Services\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Router;


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
    
        header('Content-Type: application/json');
        header('X-Requested-With: XMLHttpRequest');
        header('Access-Control-Allow-Headers: Authorization');

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
    
}

