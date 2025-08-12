<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CarResource;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use function activity;

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

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['Kennzeichen' => $car->Kennzeichen])
                ->log('Fahrzeug erstellt: ' . $car->Kennzeichen . 'von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

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
        $maxPerPage = 100;
        $perPage = min((int) request()->input('itemsPerPage', 20), $maxPerPage);
        $page = max((int) request()->input('page', 1), 1);
        $sortBy = request()->input('sortBy', 'id');
        $sortDesc = filter_var(request()->input('sortDesc', 'false'), FILTER_VALIDATE_BOOLEAN);

        $query = Car::with('images');
        if (!empty($sortBy)) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        $total = $query->count();

        $cars = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'items' => CarResource::collection($cars),
            'total' => $total,
        ]);
    }

    /**
     * Car Search
     */
    public function search(Request $request)
    {
        $maxPerPage = 100;
        try {
            $query = $request->input('query', '');
            $perPage = min((int) request()->input('itemsPerPage', 20), $maxPerPage);
            $page = $request->input('page', 1);
            $sortBy = $request->input('sortBy', 'id');
            $sortDesc = $request->input('sortDesc', 'false') === 'true';

            $allowedSortFields = ['id', 'Kennzeichen', 'Fahrzeugklasse', 'Automarke', 'Typ', 'Farbe', 'Sonstiges'];

            // If the query is empty, return an empty result
            if (empty(trim($query))) {
                return response()->json([
                    'items' => [],
                    'total' => 0,
                ]);
            }

            $queryBuilder = Car::with('images');

            // Search by all main fields
            $queryBuilder->where(function ($q) use ($query) {
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

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['Kennzeichen' => $car->Kennzeichen])
                ->log('Fahrzeug gelöscht: ' . $car->Kennzeichen . ' von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            
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

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['Kennzeichen' => implode(', ', $kennzeichen)])
                ->log('Mehrere Fahrzeuge gelöscht: ' . implode(', ', $kennzeichen) . ' von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

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
                    $path = $image->store('cars', 'public');
                    $car->images()->create(['path' => $path]);
                }
            }

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['Kennzeichen' => $car->Kennzeichen])
                ->log('Fahrzeug aktualisiert: ' . $car->Kennzeichen . ' von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            $car->load('images');

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

    public function countCars()
    {
        try {
            $count = Car::count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Fehler beim Zählen der Fahrzeuge: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Zählen der Fahrzeuge'], 500);
        }
    }
}
