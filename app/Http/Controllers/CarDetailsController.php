<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

    public function update(Request $request, $kennzeichen)
    {
        try {
            $kennzeichen = urldecode($kennzeichen);
            $kennzeichen = str_replace('+', ' ', $kennzeichen);
            $car = Car::where('Kennzeichen', $kennzeichen)->firstOrFail();

            $validatedData = $request->validate([
                'Kennzeichen' => 'required|string',
                'Fahrzeugklasse' => 'nullable|integer',
                'Automarke' => 'nullable|string',
                'Typ' => 'nullable|string',
                'Farbe' => 'nullable|string',
                'Sonstiges' => 'nullable|string',
                'customer_id' => 'nullable|exists:customers,id',
            ]);

            $updated = $car->update($validatedData);

            if (!$updated) {
                throw new \Exception('Failed to update car data');
            }

            return response()->json([
                'success' => true,
                'message' => 'Fahrzeugdaten aktualisiert',
                'data' => new CarResource($car->fresh())
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fahrzeug nicht gefunden'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating car: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()
            ], 500);
        }
    }
}
