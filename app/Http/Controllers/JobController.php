<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

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
        ]);

        $job = Job::create($validatedData);
        $job->services()->sync($request->input('service_id'));

        return response()->json($job, 201);
    }

    public function index(Request $request)
    {
        $query = Job::with('services');

        $itemsPerPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = $request->input('sortDesc', 'true') === 'true';

        $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');

        $jobs = $query->paginate($itemsPerPage);

        return response()->json([
            'items' => $jobs->items(),
            'total' => $jobs->total(),
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
        ]);

        $job->update($validatedData);
        $job->services()->sync($request->input('service_id'));

        return response()->json($job);
    }

    public function destroy(Job $job)
    {
        $job->delete();

        return response()->json(null, 204);
    }
}
