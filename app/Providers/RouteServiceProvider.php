<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    public function boot()
    {
        parent::boot();
        //benutzerdefinierte Bindung für den URL Parameter Kennzeichen
        Route::bind('Kennzeichen', function ($value) {
            //Url wird in ein Car Model umgewandelt und übergeben
            //Wenn kein Fahrzeug gefunden wird, wird eine 404-Fehlerseite angezeigt
            //Wenn ein Fahrzeug gefunden wird, wird es zurückgegeben
            return \App\Models\Car::where('Kennzeichen', $value)->firstOrFail();
        });
    }

    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
