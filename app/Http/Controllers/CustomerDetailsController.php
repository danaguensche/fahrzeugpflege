<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;

class CustomerDetailsController extends CustomerController
{
    public function details($id)
    {
        $customer = Customer::with(['cars'])->where('id', $id)->first();

        if ($customer !== null) {
            return new CustomerResource($customer);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kunde nicht gefunden',
            ], 404);
        }
    }
}
