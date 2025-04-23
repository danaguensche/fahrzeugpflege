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
                'Fahrzeugklasse' => 'nullable|integer',
                'Automarke' => 'nullable|string',
                'Typ' => 'nullable|string',
                'Farbe' => 'nullable|string',
                'Sonstiges' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $car = Car::create($validatedData);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('cars');
                    $car->images()->create(['path' => $path]);
                }
            }
            $car->load('images');

            return response()->json([
                'message' => 'Fahrzeug erfolgreich gespeichert',
                'car' => new CarResource($car),
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
        $query = Car::with('images');
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

    public function show($kennzeichen)
    {
        $car = Car::with('images')->where('Kennzeichen', $kennzeichen)->firstOrFail();
        return new CarResource($car);
    }



    public function destroy($kennzeichen)
    {
        $car = Car::where('Kennzeichen', $kennzeichen)->first();

        if ($car) {
            foreach ($car->images as $image) {
                Storage::disk('cars/')->delete($image->path);
                $image->delete();
            }

            $car->delete();
            return response()->json(['success' => true, 'message' => 'Fahrzeug und Bilder wurden gelöscht.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Fahrzeug nicht gefunden.'], 404);
        }
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'Keine IDs angegeben.'], 400);
        }

        $cars = Car::whereIn('id', $ids)->with('images')->get();
        foreach ($cars as $car) {
            foreach ($car->images as $image) {
                Storage::disk('cars/')->delete($image->path);
                $image->delete();
            }
            $car->delete();
        }

        return response()->json(['success' => true, 'message' => 'Fahrzeuge gelöscht.']);
    }

    public function update(Request $request, $kennzeichen)
    {
        try {
            $car = Car::where('Kennzeichen', $kennzeichen)->firstOrFail();

            $validatedData = $request->validate([
                'Kennzeichen' => 'required|string',
                'Fahrzeugklasse' => 'nullable|integer',
                'Automarke' => 'nullable|string',
                'Typ' => 'nullable|string',
                'Farbe' => 'nullable|string',
                'Sonstiges' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $car->update($validatedData);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('cars');
                    $car->images()->create(['path' => $path]);
                }
            }

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
