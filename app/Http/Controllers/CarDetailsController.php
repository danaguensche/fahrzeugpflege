<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;

class CarDetailsController extends CarController
{
    public function details($kennzeichen)
    {
        $kennzeichen = str_replace('+', ' ', $kennzeichen);
        $car = Car::with(['images', 'customer'])->where('Kennzeichen', $kennzeichen)->first();

        if ($car !== null) {
            return new CarResource($car);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Fahrzeug nicht gefunden',
            ], 404);
        }
    }
}
