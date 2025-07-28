<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class JobController extends Controller
{
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'car_id' => 'required|exists:cars,id',
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'trainee_id' => 'nullable|exists:users,id',
        ]);

        $user = auth()->user();
        if (!$user) {
            // Handle unauthenticated user, e.g., throw an exception or return an error response
            abort(401, 'Unauthenticated.');
        }
        $job = Job::create(array_merge($validatedData, ['trainer_id' => $user->id]));
        $job->services()->sync($request->input('service_ids'));

        return response()->json($job, 201);
    }

    public function index(Request $request)
    {
        $maxPerPage = 100;
        $perPage = min((int) $request->input('itemsPerPage', 20), $maxPerPage);
        $page = max((int) $request->input('page', 1), 1);
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = filter_var($request->input('sortDesc', 'false'), FILTER_VALIDATE_BOOLEAN);

        $allowedSortFields = ['id', 'title', 'description', 'scheduled_at', 'status'];

        $query = Job::with(['customer', 'car', 'services', 'trainer', 'trainee']);

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
        $searchQuery = $request->input('query');
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = $request->input('sortDesc', 'true') === 'true';

        $query = Job::where('title', 'like', '%' . $searchQuery . '%')
            ->orWhere('description', 'like', '%' . $searchQuery . '%');

        $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');

        $jobs = $query->paginate($itemsPerPage);

        return response()->json([
            'items' => $jobs->items(),
            'total' => $jobs->total(),
        ]);
    }

    public function show(Job $job)
    {
        return response()->json($job->load(['services', 'comments.user']));
    }

    public function update(Request $request, Job $job)
    {
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

            return response()->json($job->load('services'));

        } else { // Admin or Trainer
            // Convert empty string for scheduled_at to null
            if ($request->has('scheduled_at') && $request->input('scheduled_at') === '') {
                $request->merge(['scheduled_at' => null]);
            }

            $validatedData = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'car_id' => 'sometimes|required|exists:cars,id',
                'customer_id' => 'sometimes|required|exists:customers,id',
                'status' => 'sometimes|required|string',
                'scheduled_at' => 'nullable|date',
                'services' => 'nullable|array',
                'services.*.id' => 'required|exists:services,id',
            ]);

            $job->update($validatedData);

            if ($request->has('services')) {
                $serviceIds = collect($request->input('services'))->pluck('id')->toArray();
                $job->services()->sync($serviceIds);
            }

            return response()->json($job->load('services'));
        }
    }

    public function destroy(Job $job)
    {
        $job->delete();

        return response()->json(null, 204);
    }



    public function destroyMultiple(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:customers,id'
            ]);

            Job::destroy($validated['ids']);

            return response()->json([
                'success' => true,
                'message' => 'Jobs wurden erfolgreich gelöscht.'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Löschen mehrerer Jobs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen der Jobs'
            ], 500);
        }
    }
}
