<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerSearchController extends Controller
{


    public function search(Request $request)
    {
        $query = $request->input('query');
        Log::info('Customer search query:', ['query' => $query]);

        if (!$query) {
            Log::info('Empty query, returning all customers.');
            $results = Customer::all();
        } else {
            $results = Customer::where('firstname', 'like', '%' . $query . '%')
                               ->orWhere('lastname', 'like', '%' . $query . '%')
                               ->orWhere('email', 'like', '%' . $query . '%')
                               ->get();
        }

        Log::info('Customer search results:', ['results_count' => $results->count(), 'results' => $results->toArray()]);

        return CustomerResource::collection($results);
    }
}
