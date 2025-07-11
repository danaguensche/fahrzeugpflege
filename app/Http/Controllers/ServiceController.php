<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return ServiceResource::collection($services);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $services = Service::where('name', 'like', '%' . $query . '%')
                            ->orWhere('description', 'like', '%' . $query . '%')
                            ->get();

        return ServiceResource::collection($services);
    }
}
