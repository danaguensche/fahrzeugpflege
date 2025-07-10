<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query();

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
}
