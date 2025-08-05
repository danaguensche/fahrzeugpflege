<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
            ]);

            $user = auth()->user();
            if (!$user) {
                abort(401, 'Unauthenticated.');
            }

            DB::beginTransaction();

            $job = Job::create(array_merge($validatedData, ['trainer_id' => $user->id]));
            $job->services()->sync($request->input('service_ids'));

            // Erweiterte Bild-Verarbeitung
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

            $job->load(['services', 'images']);

            return response()->json([
                'message' => 'Job erfolgreich gespeichert',
                'job' => $job,
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

            $query->where(function($q) use ($searchQuery) {
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
                Log::info('JobController@update: Trainee user detected.', ['user_id' => $user->id, 'job_user_id' => $job->user_id, 'job_id' => $job->id]);
                
                // Trainee can only update status for their own jobs
                if ($job->trainee_id !== $user->id) {
                    Log::error('JobController@update: Trainee attempting to update job not assigned to them.', ['user_id' => $user->id, 'job_trainee_id' => $job->trainee_id, 'job_id' => $job->id]);
                    abort(403, 'Unauthorized action. You can only update your own jobs.');
                }

                // Validate that only 'status' is being updated
                $allowedFields = ['status'];
                Log::info('JobController@update: Request all for trainee.', ['request_all' => $request->all()]);
                $requestFields = array_keys($request->all());
                Log::info('JobController@update: Trainee update request fields.', ['request_fields' => $requestFields]);
                $diff = array_diff($requestFields, $allowedFields);

                if (!empty($diff)) {
                    Log::error('JobController@update: Trainee attempting to update fields other than status.', ['user_id' => $user->id, 'job_id' => $job->id, 'attempted_fields' => $requestFields]);
                    abort(403, 'Unauthorized action. Trainees can only update job status.');
                }

                $validatedData = $request->validate([
                    'status' => 'required|string',
                ]);

                $job->update($validatedData);

                Log::info('Job trainee_id after update', ['trainee_id' => $job->trainee_id]);

                return response()->json([
                    'message' => 'Job Status erfolgreich aktualisiert',
                    'job' => $job->load('services')
                ]);

            } else { // Admin or Trainer
                DB::beginTransaction();

                // Convert empty string for scheduled_at to null
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
                ]);

                $job->update($validatedData);

                Log::info('Job trainee_id after update', ['trainee_id' => $job->trainee_id]);

                if ($request->has('services')) {
                    $serviceIds = collect($request->input('services'))->pluck('id')->toArray();
                    $job->services()->sync($serviceIds);
                }

                // Handle new images
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

                Log::info('JobController@update payload', $request->all());
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
            $image = $job->images()->findOrFail($imageId);
            
            $imagePath = str_replace('storage/', '', $image->path);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bild erfolgreich gelöscht.'
            ]);

        } catch (\Exception $e) {
            Log::error('Fehler beim Löschen des Bildes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen des Bildes.'
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
                
                // Stelle sicher, dass alle erforderlichen Felder gesetzt sind
                $imageData = [
                    'path' => $path,
                    'user_id' => $user->id,
                ];
                
                $newImage = $job->images()->create($imageData);
                $addedImages[] = $newImage;
            }

            DB::commit();

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
}