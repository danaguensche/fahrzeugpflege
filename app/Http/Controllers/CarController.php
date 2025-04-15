<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CarResource;
use Illuminate\Support\Facades\Storage;

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
                'image' => 'nullable|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $car = new Car();
            $car->fill($validatedData);

            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('cars');
                $car->image = Storage::url($image);
            }

            $car->save();

            return response()->json([
                'message' => 'Fahrzeug erfolgreich gespeichert',
                'car' => new CarResource($car),
                'image_path' => $car->image
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Speichern des Fahrzeugs: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Speichern des Fahrzeugs'], 500);
        }
    }

    public function index()
    {

        $perPage = request()->input('itemsPerPage', 20);
        $page = request()->input('page', 1);
        $sortBy = request()->input('sortBy', 'id');
        $sortDesc = request()->input('sortDesc', 'false') === 'true';
        $query = Car::query();
        if ($sortBy) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        $total = $query->count();
        $cars = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        return response()->json([
            'items' => CarResource::collection($cars),
            'total' => $total
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
            if ($car->image) {
                $path = str_replace('/storage/', '', $car->image);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $car->delete();
            return response()->json(['success' => true, 'message' => 'Fahrzeug wurde gelöscht.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Fahrzeug nicht gefunden.'], 404);
        }
    }

    public function update(Request $request, Car $car)
    {
        try {
            $validatedData = $request->validate([
                'Kennzeichen' => 'required|string',
                'Fahrzeugklasse' => 'nullable|string',
                'Automarke' => 'nullable|string',
                'Typ' => 'nullable|string',
                'Farbe' => 'nullable|string',
                'Sonstiges' => 'nullable|string',
                'image' => 'nullable|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $car->fill($validatedData);

            if ($request->hasFile('image')) {
                if ($car->image) {
                    $path = str_replace('/storage/', '', $car->image);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }

                $image = $request->file('image')->store('cars', 'public');
                $car->image = Storage::url($image);
            }

            $car->save();

            return response()->json([
                'message' => 'Fahrzeug erfolgreich aktualisiert',
                'car' => new CarResource($car)
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Aktualisieren des Fahrzeugs: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Aktualisieren des Fahrzeugs'], 500);
        }
    }
}
