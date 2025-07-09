<?php

namespace App\Http\Controllers;

use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;

class CarSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            // If no query, return all cars (or an empty array, depending on desired behavior)
            // For now, let's return an empty array if no query is provided.
            return CarResource::collection(Car::all());
        }

        $results = Car::where('Kennzeichen', 'LIKE', '%' . $query . '%')
                       ->orWhere('Automarke', 'LIKE', '%' . $query . '%')
                       ->orWhere('Typ', 'LIKE', '%' . $query . '%')
                       ->get();

        return CarResource::collection($results);
    }
}