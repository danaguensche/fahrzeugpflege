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
            $results = Car::all();
        } else {
            $results = Car::search($query)->get();
        }

        return CarResource::collection($results);
    }
}
