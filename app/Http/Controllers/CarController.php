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
                'Kennzeichen' => 'required|string|max:10|unique:cars,Kennzeichen',
                'Fahrzeugklasse' => 'nullable|string|max:24',
                'Automarke' => 'nullable|string|max:24',
                'Typ' => 'nullable|string|max:24',
                'Farbe' => 'nullable|string|max:24',
                'Sonstiges' => 'nullable|string|max:65000',
                'customer_id' => 'nullable|exists:customers,id',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ], [
                'Kennzeichen.unique' => 'Fahrzeug existiert bereits.',
                'Kennzeichen.required' => 'Kennzeichen ist erforderlich.',
            ]);

            $car = Car::create($validatedData);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('cars', 'public');
                    $car->images()->create(['path' => $path]);
                }
            }

            $car->load('images');

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['Kennzeichen' => $car->Kennzeichen])
                ->log('Fahrzeug erstellt: ' . $car->Kennzeichen . ' von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            return response()->json([
                'message' => 'Fahrzeug erfolgreich gespeichert',
                'car' => new CarResource($car),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Die eingegebenen Daten sind ungültig.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Speichern des Fahrzeugs: ' . $e->getMessage());
            return response()->json([
                'message' => 'Fehler beim Speichern des Fahrzeugs'
            ], 500);
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

    public function searchAvailableCars(Request $request)
    {
        try {
            $query = $request->input('query', '');

            if (empty(trim($query))) {
                $cars = Car::whereNull('customer_id')
                    ->select('id', 'Kennzeichen', 'Automarke', 'Typ')
                    ->take(10)
                    ->get();

                return response()->json($cars);
            }

            // Suche nach verfügbaren Fahrzeugen
            $cars = Car::whereNull('customer_id')
                ->where(function ($q) use ($query) {
                    $searchTerm = '%' . $query . '%';
                    $q->where('Kennzeichen', 'like', $searchTerm)
                        ->orWhere('Automarke', 'like', $searchTerm)
                        ->orWhere('Typ', 'like', $searchTerm);
                })
                ->select('id', 'Kennzeichen', 'Automarke', 'Typ')
                ->take(10)
                ->get();

            return response()->json($cars);
        } catch (\Exception $e) {
            Log::error('Fehler bei der Suche verfügbarer Fahrzeuge: ' . $e->getMessage());
            return response()->json([], 500);
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
                'Kennzeichen' => [
                    'required',
                    'string',
                    'max:10',
                    Rule::unique('cars', 'Kennzeichen')->ignore($car->id)
                ],
                'Fahrzeugklasse' => 'nullable|string|max:24',
                'Automarke' => 'nullable|string|max:24',
                'Typ' => 'nullable|string|max:24',
                'Farbe' => 'nullable|string|max:24',
                'Sonstiges' => 'nullable|string|max:65000',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ], [
                'Kennzeichen.unique' => 'Dieses Kennzeichen existiert bereits.',
                'Kennzeichen.required' => 'Kennzeichen ist erforderlich.',
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
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Aktualisieren des Fahrzeugs: ' . $e->getMessage());
            return response()->json(['message' => 'Fehler beim Aktualisieren des Fahrzeugs'], 500);
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

    public function availableCars()
    {
        try {
            $cars = Car::where('customer_id', null)->get();
        } catch (\Exception $e) {
            Log::error('Fehler beim Abrufen der verfügbaren Fahrzeuge: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Abrufen der verfügbaren Fahrzeuge'], 500);
        }
    }

    public function getAllImages($kennzeichen)
    {
        try {
            $car = Car::where('Kennzeichen', $kennzeichen)->firstOrFail();
            Log::info('Car found:', ['id' => $car->id]);

            $carImages = $car->images()->get()->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => 'storage/' . $image->path,
                    'file_name' => $image->file_name ?? null,
                    'type' => 'car_image',
                    'source' => 'Fahrzeug',
                    'created_at' => $image->created_at,
                ];
            });

            $jobImages = DB::table('image_reports')
                ->where('car_id', $car->id)
                ->get()
                ->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'path' => 'storage/' . $image->path,
                        'file_name' => $image->file_name ?? null,
                        'type' => 'job_image',
                        'source' => 'Auftrag #' . $image->job_id,
                        'job_id' => $image->job_id,
                        'created_at' => $image->created_at,
                    ];
                });

            $allImages = $carImages->concat($jobImages)
                ->sortByDesc('created_at')
                ->values();

            Log::info('Images loaded:', [
                'car_images' => $carImages->count(),
                'job_images' => $jobImages->count(),
                'total' => $allImages->count()
            ]);

            return response()->json([
                'success' => true,
                'images' => $allImages,
                'car_images_count' => $carImages->count(),
                'job_images_count' => $jobImages->count(),
                'total_count' => $allImages->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllImages:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Bilder',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
