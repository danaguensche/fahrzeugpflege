<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
    public function boot()
    {
        Validator::resolver(function($translator, $data, $rules, $messages) {
            return new class($translator, $data, $rules, $messages) extends \Illuminate\Validation\Validator {
            };
        });
    }    
}
