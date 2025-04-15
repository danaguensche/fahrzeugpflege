<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarDetailsController extends CarController
{
    public function details($kennzeichen)
    {
        $kennzeichen = str_replace('+', ' ', $kennzeichen);
        $car = Car::where('Kennzeichen', $kennzeichen)->first();
        if ($car !== null) {
            // Decode the image if it exists
            // if ($car->image) {
            //     $car->image = base64_decode($car->image);
            // }
            // Return the car details as a JSON response
            return response()->json([
                'data' => $car,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Fahrzeug nicht gefunden',
            ], 404);
        }
    }
}
