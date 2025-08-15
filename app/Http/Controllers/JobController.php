<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use \App\Models\Customer;
use \App\Models\Car;

class JobController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'car_id' => 'required|exists:cars,id',
                'customer_id' => 'required|exists:customers,id',
                'user_id' => 'nullable|exists:users,id',
                'status' => 'required|string',
                'scheduled_at' => 'nullable|date',
                'service_ids' => 'required|array',
                'service_ids.*' => 'exists:services,id',
                'trainee_id' => 'nullable|exists:users,id',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
                'assign_car_to_customer' => 'boolean'
            ]);

            $user = auth()->user();
            if (!$user) {
                abort(401, 'Unauthenticated.');
            }

            DB::beginTransaction();

            $job = Job::create(array_merge($validatedData, ['trainer_id' => $user->id]));
            $job->services()->sync($request->input('service_ids'));

            // Automatische Fahrzeug-Zuweisung
            $assignCar = $request->input('assign_car_to_customer', true);
            if ($assignCar) {
                $car = \App\Models\Car::find($validatedData['car_id']);
                if ($car && (!$car->customer_id || $car->customer_id == 0)) {
                    $car->update(['customer_id' => $validatedData['customer_id']]);
                    Log::info('Fahrzeug automatisch dem Kunden zugewiesen', [
                        'car_id' => $car->id,
                        'customer_id' => $validatedData['customer_id'],
                        'job_id' => $job->id
                    ]);
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('jobs', 'public');
                    $job->images()->create([
                        'path' => $path,
                        'user_id' => $user->id,
                    ]);
                }
            }

            DB::commit();

            $job->load(['services', 'images', 'car', 'customer']);

            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'job_id' => $job->id,
                    'title' => $job->title,
                    'status' => $job->status,
                    'car_assigned' => $assignCar
                ])
                ->log('Auftrag erstellt: ' . $job->title . ' mit Status ' . $job->status . ' von ' . $user->firstname . ' ' . $user->lastname);

            return response()->json([
                'message' => 'Job erfolgreich gespeichert',
                'job' => $job,
                'car_assigned' => $assignCar
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fehler beim Speichern des Jobs: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Speichern des Jobs'], 500);
        }
    }


    public function index(Request $request)
    {
        $maxPerPage = 100;
        $perPage = min((int) $request->input('itemsPerPage', 20), $maxPerPage);
        $page = max((int) $request->input('page', 1), 1);
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = filter_var($request->input('sortDesc', 'false'), FILTER_VALIDATE_BOOLEAN);

        $allowedSortFields = ['id', 'title', 'description', 'scheduled_at', 'status'];

        $query = Job::with(['customer', 'car', 'services', 'trainer', 'trainee', 'images']);

        // Filter by user role
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->role === 'trainee') {
            $query->where('trainee_id', $user->id);
        }

        // Filtering by status
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        // Filtering by car_id
        if ($request->has('car_id') && $request->input('car_id') !== '') {
            $query->where('car_id', $request->input('car_id'));
        }

        // Filtering by customer_id
        if ($request->has('customer_id') && $request->input('customer_id') !== '') {
            $query->where('customer_id', $request->input('customer_id'));
        }

        // Filtering by user_id (for trainer/admin to filter by specific trainee)
        if ($user && $user->role !== 'trainee' && $request->has('user_id') && $request->input('user_id') !== '') {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->input('start'));
            $end = Carbon::parse($request->input('end'));
            $query->whereBetween('scheduled_at', [$start, $end]);
        }

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        $total = $query->count();

        $jobs = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        Log::info('Jobs fetched:', ['count' => $jobs->count(), 'jobs' => $jobs->toArray()]);

        return response()->json([
            'items' => $jobs,
            'total' => $total,
        ]);
    }

    public function search(Request $request)
    {
        $maxPerPage = 100;
        try {
            $searchQuery = $request->input('query', '');
            $itemsPerPage = min((int) $request->input('itemsPerPage', 10), $maxPerPage);
            $page = $request->input('page', 1);
            $sortBy = $request->input('sortBy', 'id');
            $sortDesc = $request->input('sortDesc', 'true') === 'true';

            $allowedSortFields = ['id', 'title', 'description', 'scheduled_at', 'status'];

            // If the query is empty, return an empty result
            if (empty(trim($searchQuery))) {
                return response()->json([
                    'items' => [],
                    'total' => 0,
                ]);
            }

            $query = Job::with(['customer', 'car', 'services', 'trainer', 'trainee', 'images']);

            $query->where(function ($q) use ($searchQuery) {
                $searchTerm = '%' . $searchQuery . '%';
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('status', 'like', $searchTerm);

                // Search by ID if the query is numeric
                if (is_numeric($searchQuery)) {
                    $q->orWhere('id', '=', (int)$searchQuery);
                }
            });

            // Apply sorting
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
            }

            $total = $query->count();
            $jobs = $query->skip(($page - 1) * $itemsPerPage)->take($itemsPerPage)->get();

            return response()->json([
                'items' => $jobs,
                'total' => $total,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in job search: ' . $e->getMessage());
            return response()->json([
                'items' => [],
                'total' => 0,
                'error' => 'Error during search'
            ], 500);
        }
    }

    public function show(Job $job)
    {
        return response()->json($job->load(['services', 'comments.user', 'images']));
    }

    public function update(Request $request, Job $job)
    {
        try {
            $user = auth()->user();

            if ($user && $user->role === 'trainee') {
                // Trainee darf nur Status updaten
                if ($job->trainee_id !== $user->id) {
                    abort(403, 'Unauthorized action. You can only update your own jobs.');
                }

                $allowedFields = ['status'];
                $requestFields = array_keys($request->all());
                $diff = array_diff($requestFields, $allowedFields);

                if (!empty($diff)) {
                    abort(403, 'Unauthorized action. Trainees can only update job status.');
                }

                $validatedData = $request->validate([
                    'status' => 'required|string',
                ]);

                $job->update($validatedData);

                activity()
                    ->causedBy($user)
                    ->withProperties([
                        'job_id' => $job->id,
                        'title' => $job->title,
                        'status' => $job->status,
                    ])
                    ->log('Auftrag bearbeitet: ' . $job->title . ' mit Status ' . $job->status . ' von ' . $user->firstname . ' ' . $user->lastname);

                return response()->json([
                    'message' => 'Job Status erfolgreich aktualisiert',
                    'job' => $job->load('services')
                ]);
            } else { // Admin oder Trainer
                DB::beginTransaction();

                if ($request->has('scheduled_at') && $request->input('scheduled_at') === '') {
                    $request->merge(['scheduled_at' => null]);
                }

                $validatedData = $request->validate([
                    'title' => 'sometimes|required|string|max:255',
                    'description' => 'nullable|string',
                    'trainee_id' => 'nullable|exists:users,id',
                    'car_id' => 'sometimes|required|exists:cars,id',
                    'customer_id' => 'sometimes|required|exists:customers,id',
                    'status' => 'sometimes|required|string',
                    'scheduled_at' => 'nullable|date',
                    'services' => 'nullable|array',
                    'services.*.id' => 'required|exists:services,id',
                    'images' => 'nullable|array',
                    'images.*' => 'nullable|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
                    'assign_car_to_customer' => 'boolean'
                ]);

                $job->update($validatedData);

                // Services aktualisieren
                if ($request->has('services')) {
                    $serviceIds = collect($request->input('services'))->pluck('id')->toArray();
                    $job->services()->sync($serviceIds);
                }

                // Automatische Fahrzeug-Zuweisung
                if ($request->has('assign_car_to_customer') && $request->boolean('assign_car_to_customer')) {
                    $car = \App\Models\Car::find($validatedData['car_id'] ?? $job->car_id);
                    if ($car && (!$car->customer_id || $car->customer_id == 0)) {
                        $car->update(['customer_id' => $validatedData['customer_id'] ?? $job->customer_id]);
                        Log::info('Fahrzeug automatisch dem Kunden zugewiesen (Update)', [
                            'car_id' => $car->id,
                            'customer_id' => $validatedData['customer_id'] ?? $job->customer_id,
                            'job_id' => $job->id
                        ]);
                    }
                }

                // Neue Bilder hochladen
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $path = $image->store('jobs', 'public');
                        $job->images()->create([
                            'path' => $path,
                            'user_id' => $user->id,
                        ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'message' => 'Job erfolgreich aktualisiert',
                    'job' => $job->load(['services', 'images'])
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fehler beim Aktualisieren des Jobs: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Aktualisieren des Jobs'], 500);
        }
    }
    public function destroy(Job $job)
    {
        try {
            DB::beginTransaction();

            // Delete all associated images from storage and database
            foreach ($job->images as $image) {
                $imagePath = str_replace('storage/', '', $image->path);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $image->delete();
            }

            $job->delete();

            DB::commit();

            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'job_id' => $job->id,
                    'title' => $job->title,
                    'status' => $job->status,
                ])
                ->log('Auftrag gelöscht: ' . $job->title . ' mit Status ' . $job->status . ' von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            return response()->json([
                'success' => true,
                'message' => 'Job und alle zugehörigen Bilder wurden gelöscht.'
            ], 204);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fehler beim Löschen des Jobs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen des Jobs.'
            ], 500);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:jobs,id' // Fixed: should be jobs, not customers
            ]);

            DB::beginTransaction();

            $jobs = Job::whereIn('id', $validated['ids'])->with('images')->get();

            if ($jobs->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Keine Jobs gefunden.'
                ], 404);
            }

            foreach ($jobs as $job) {
                // Delete all associated images from storage and database
                foreach ($job->images as $image) {
                    $imagePath = str_replace('storage/', '', $image->path);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    $image->delete();
                }
                $job->delete();
            }

            DB::commit();

            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'job_ids' => $validated['ids'],
                    'count' => count($jobs),
                ])
                ->log('Mehrere Aufträge gelöscht: ' . count($jobs) . ' Jobs von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            return response()->json([
                'success' => true,
                'message' => count($jobs) . ' Jobs wurden erfolgreich gelöscht.'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fehler beim Löschen mehrerer Jobs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen der Jobs'
            ], 500);
        }
    }

    /**
     * Delete a single image from a job
     */
    public function deleteImage(Request $request, Job $job, $imageId)
    {
        try {
            Log::info('Attempting to delete image', [
                'job_id' => $job->id,
                'image_id' => $imageId
            ]);

            // Find the image that belongs to this job
            $image = $job->images()->where('id', $imageId)->first();

            if (!$image) {
                Log::warning('Image not found', [
                    'job_id' => $job->id,
                    'image_id' => $imageId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Bild nicht gefunden oder gehört nicht zu diesem Auftrag.'
                ], 404);
            }

            DB::beginTransaction();

            // Delete the physical file from storage
            $imagePath = str_replace('storage/', '', $image->path);
            Log::info('Attempting to delete file from storage', ['path' => $imagePath]);

            if (Storage::disk('public')->exists($imagePath)) {
                $deleted = Storage::disk('public')->delete($imagePath);
                Log::info('File deletion result', ['deleted' => $deleted, 'path' => $imagePath]);
            } else {
                Log::warning('File not found in storage', ['path' => $imagePath]);
            }

            // Delete the database record
            $image->delete();

            DB::commit();

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'job_id' => $job->id,
                    'image_id' => $imageId,
                    'image_path' => $image->path
                ])
                ->log('Bild gelöscht von Auftrag: ' . $job->title);

            Log::info('Image successfully deleted', [
                'job_id' => $job->id,
                'image_id' => $imageId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bild erfolgreich gelöscht.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fehler beim Löschen des Bildes', [
                'job_id' => $job->id,
                'image_id' => $imageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen des Bildes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new images to existing job
     */
    public function addImages(Request $request, Job $job)
    {
        try {
            $validatedData = $request->validate([
                'images' => 'required|array',
                'images.*' => 'required|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $user = auth()->user();
            if (!$user) {
                abort(401, 'Unauthenticated.');
            }

            DB::beginTransaction();

            $addedImages = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('jobs', 'public');

                $imageData = [
                    'path' => $path,
                    'user_id' => $user->id,
                ];

                $newImage = $job->images()->create($imageData);
                $addedImages[] = $newImage;
            }

            DB::commit();

            activity()
                ->causedBy($user)
                ->withProperties([
                    'job_id' => $job->id,
                    'title' => $job->title,
                    'status' => $job->status,
                    'added_images_count' => count($addedImages),
                ])
                ->log('Bilder hinzugefügt zum Auftrag: ' . $job->title . ' von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            return response()->json([
                'success' => true,
                'message' => count($addedImages) . ' Bilder erfolgreich hinzugefügt.',
                'images' => $addedImages
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fehler beim Hinzufügen der Bilder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Hinzufügen der Bilder.'
            ], 500);
        }
    }

    public function getOpenJobsCount()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                abort(401, 'Unauthenticated.');
            }

            $query = Job::query();

            // Filter based on user role
            if ($user->role === 'trainee') {
                $query->where('trainee_id', $user->id);
            }

            $openStatusList = ['ausstehend'];
            $openJobsCount = $query->whereIn('status', $openStatusList)->count();

            return response()->json([
                'count' => $openJobsCount
            ]);
        } catch (\Exception $e) {
            Log::error('Fehler beim Abrufen der offenen Aufträge: ' . $e->getMessage());
            return response()->json([
                'error' => 'Fehler beim Abrufen der offenen Aufträge'
            ], 500);
        }
    }

    public function getTodayJobsCount()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                abort(401, 'Unauthenticated.');
            }

            $today = Carbon::today();
            $query = Job::query();

            // Filter based on user role
            if ($user->role === 'trainee') {
                // Trainee sees only their assigned jobs
                $query->where('trainee_id', $user->id);
            }
            // Count jobs scheduled for today
            $todayJobsCount = $query->whereDate('scheduled_at', $today)->count();

            return response()->json([
                'count' => $todayJobsCount,
                'date' => $today->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            Log::error('Fehler beim Abrufen der heutigen Aufträge: ' . $e->getMessage());
            return response()->json([
                'error' => 'Fehler beim Abrufen der heutigen Aufträge'
            ], 500);
        }
    }

    public function getAvailableCars(Request $request)
    {
        try {
            $cars = Car::whereNull('customer_id')
                ->orderBy('license_plate')
                ->get();

            return response()->json([
                'cars' => $cars
            ]);
        } catch (\Exception $e) {
            Log::error('Fehler beim Abrufen verfügbarer Fahrzeuge: ' . $e->getMessage());
            return response()->json([
                'error' => 'Fehler beim Abrufen verfügbarer Fahrzeuge'
            ], 500);
        }
    }

    public function getCarsForCustomer($customerId)
    {
        $customer = Customer::with('cars')->findOrFail($customerId);

        if ($customer->cars()->exists()) {
            $cars = $customer->cars()->get();
        } else {
            $cars = Car::whereNull('customer_id')->get();
        }

        return response()->json([
            'cars' => $cars
        ]);
    }
}
