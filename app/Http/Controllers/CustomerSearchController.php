<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerSearchController extends Controller
{


    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['data' => []]);
        }

        $results = Customer::search($query)->get();

        return CustomerResource::collection($results);
    }
}
