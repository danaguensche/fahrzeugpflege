<?php

namespace App\Http\Controllers;

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
        ]);

        $job = Job::create($validatedData);
        $job->services()->sync($request->input('service_ids'));

        return response()->json($job, 201);
    }

    public function index(Request $request)
    {
        $query = Job::with(['customer', 'car', 'services']);

        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->input('start'));
            $end = Carbon::parse($request->input('end'));
            $query->whereBetween('scheduled_at', [$start, $end]);
        }

        $jobs = $query->get();

        return response()->json($jobs);
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
        return response()->json($job->load('services'));
    }

    public function update(Request $request, Job $job)
    {
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
