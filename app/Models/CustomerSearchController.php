<?php

namespace App\Http\Controllers;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerSearchController extends Controller
{
    

    public function search(Request $request)
    {
        die('Test');
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['data' => []]);
        }

        $results = Customer::search($query)->get();
        dd($results);

        return CustomerResource::collection($results);
    }
}
