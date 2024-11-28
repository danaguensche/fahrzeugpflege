<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarController extends Controller
{
    public function store(Request $request)
    {
        try {
            $car = new Car();
            $car->Kennzeichen = $request->Kennzeichen;
            $car->Fahrzeugklasse = $request->Fahrzeugklasse;
            $car->Automarke = $request->Automarke;
            $car->Typ = $request->Typ;
            $car->Farbe = $request->Farbe;
            $car->Sonstiges = $request->Sonstiges;
            $car->save();
            
            return response()->json(['message' => 'Fahrzeug erfolgreich gespeichert'], 201);
        } catch (\Exception $e) {
            Log::error('Fehler beim Speichern des Fahrzeugs: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Speichern des Fahrzeugs'], 500);
        }
    }
}
