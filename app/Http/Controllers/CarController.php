<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CarResource;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'Kennzeichen' => 'required|string|unique:cars',
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
                    $path = $image->store('cars', 'public');

                    $car->images()->create([
                        'path' => $path,
                    ]);
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
        $sortBy = request()->input('sortBy', 'Kennzeichen');

        // By default (asc)
        $sortDesc = filter_var(request()->input('sortDesc', true), FILTER_VALIDATE_BOOLEAN);

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

    /**
     * NEW METHOD: Car Search
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query', '');
            $perPage = $request->input('itemsPerPage', 20);
            $page = $request->input('page', 1);
            $sortBy = $request->input('sortBy', 'Kennzeichen');
            $sortDesc = filter_var(request()->input('sortDesc', true), FILTER_VALIDATE_BOOLEAN);

            $allowedSortFields = ['Kennzeichen', 'Fahrzeugklasse', 'Automarke', 'Typ', 'Farbe', 'Sonstiges'];

            // If the query is empty, return an empty result
            if (empty(trim($query))) {
                return response()->json([
                    'items' => [],
                    'total' => 0,
                ]);
            }

            $queryBuilder = Car::with('images');

            // Search by all main fields
            $queryBuilder->where(function($q) use ($query) {
                $searchTerm = '%' . $query . '%';
                $q->where('Kennzeichen', 'like', $searchTerm)
                  ->orWhere('Automarke', 'like', $searchTerm)
                  ->orWhere('Typ', 'like', $searchTerm)
                  ->orWhere('Farbe', 'like', $searchTerm)
                  ->orWhere('Sonstiges', 'like', $searchTerm);
                
                // Search by ID if the query is numeric
                if (is_numeric($query)) {
                    $q->orWhere('id', '=', (int)$query);
                    // Search by Fahrzeugklasse if the query is numeric
                    $q->orWhere('Fahrzeugklasse', '=', (int)$query);
                }
            });

            // Applying sorting
            if (in_array($sortBy, $allowedSortFields)) {
                $queryBuilder->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
            }

            $total = $queryBuilder->count();
            $cars = $queryBuilder->skip(($page - 1) * $perPage)->take($perPage)->get();

            return response()->json([
                'items' => CarResource::collection($cars),
                'total' => $total,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in car search: ' . $e->getMessage());
            return response()->json([
                'items' => [],
                'total' => 0,
                'error' => 'Error during search'
            ], 500);
        }
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
                $imagePath = str_replace('storage/', '', $image->path);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
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
        try {
            $kennzeichen = $request->input('kennzeichen');

            if (!is_array($kennzeichen) || empty($kennzeichen)) {
                return response()->json([
                    'error' => 'Keine Kennzeichen angegeben.'
                ], 400);
            }

            DB::beginTransaction();

            $cars = Car::whereIn('Kennzeichen', $kennzeichen)->with('images')->get();

            if ($cars->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Keine Fahrzeuge gefunden.'
                ], 404);
            }

            foreach ($cars as $car) {
                foreach ($car->images as $image) {
                    $imagePath = str_replace('storage/', '', $image->path);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    $image->delete();
                }
                $car->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($cars) . ' Fahrzeuge wurden gelöscht.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fehler beim Löschen mehrerer Fahrzeuge: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen der Fahrzeuge.'
            ], 500);
        }
    }
    public function uploadImages(Request $request, $kennzeichen)
    {
        $car = Car::where('Kennzeichen', $kennzeichen)->firstOrFail();

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:16384|mimes:jpeg,png,jpg,gif,svg',
        ]);

        foreach ($request->file('images') as $image) {
            $path = $image->store('cars', 'public');
            $car->images()->create(['path' => $path]);
        }

        $car->load('images');

        return response()->json([
            'message' => 'Bilder erfolgreich hochgeladen',
            'car' => new CarResource($car),
        ]);
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