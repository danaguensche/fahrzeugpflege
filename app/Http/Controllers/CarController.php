<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CarResource;
use App\Http\Resources\CarCollection;



class CarController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'Kennzeichen' => 'required|string',
                'Fahrzeugklasse' => 'nullable|string',
                'Automarke' => 'nullable|string',
                'Typ' => 'nullable|string',
                'Farbe' => 'nullable|string',
                'Sonstiges' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);


            $car = new Car();
            $car->fill($validatedData);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $car->image = base64_encode(file_get_contents($image));
            }

            $car->save();

            return response()->json(['message' => 'Fahrzeug erfolgreich gespeichert'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Speichern des Fahrzeugs: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Speichern des Fahrzeugs'], 500);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 20);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', 'Kennzeichen');
        $sortDesc = $request->input('sortDesc', 'false') === 'true';

        $query = Car::query();

        if ($sortBy) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        $total = $query->count();
        $cars = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'items' => CarResource::collection($cars),
            'total' => $total,
        ]);
    }


    public function show(Car $car)
    {
        return new CarResource($car);
    }

    public function destroy($kennzeichen)
    {
        $car = Car::where('Kennzeichen', $kennzeichen)->first();
        if ($car) {
            $car->delete();
            return response()->json(['success' => true, 'message' => 'Fahrzeug wurde gelöscht.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Fahrzeug nicht gefunden.'], 404);
        }
    }
    
}
