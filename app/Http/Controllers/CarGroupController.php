<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CarGroup;
use Illuminate\Http\Request;
use App\Http\Resources\CarGroupResource;
use Illuminate\Support\Facades\Log;

class CarGroupController extends Controller
{
    public function index()
    {
        $maxPerPage = 100;
        $perPage = min((int) request()->input('itemsPerPage', 20), $maxPerPage);
        $page = max((int) request()->input('page', 1), 1);
        $sortBy = request()->input('sortBy', 'id');
        $sortDesc = filter_var(request()->input('sortDesc', 'false'), FILTER_VALIDATE_BOOLEAN);

        $allowedSortFields = ['id', 'title'];

        $query = CarGroup::query();

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        $total = $query->count();
        $cargroups = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'items' => CarGroupResource::collection($cargroups),
            'total' => $total,
        ]);
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query', '');
            
            $queryBuilder = CarGroup::query();
            
            // Nur filtern wenn query nicht leer ist
            // Wenn leer, gib alle zurück (für initial load)
            if (!empty(trim($query))) {
                $queryBuilder->where('title', 'like', '%' . $query . '%');
            }
            
            // Sortierung
            $queryBuilder->orderBy('title', 'asc');
            
            // Limit setzen
            $queryBuilder->limit(100);
            
            $cargroups = $queryBuilder->get();
            
            return response()->json([
                'data' => CarGroupResource::collection($cargroups),
                'total' => $cargroups->count(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in cargroup search: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'total' => 0,
                'error' => 'Error during search'
            ], 500);
        }
    }
}