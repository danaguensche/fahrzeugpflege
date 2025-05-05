<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

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



    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::where('id', $id)->firstOrFail();

            $validatedData = $request->validate([
                'company' => 'nullable|string|max:255',
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('customers', 'email')->ignore($id)],
                'phonenumber' => 'required|string',
                'addressline' => 'required|string',
                'postalcode' => 'required|string',
                'city' => 'required|string',
            ]);

            $updated = $customer->update($validatedData);

            if (!$updated) {
                throw new \Exception('Failed to update customer data');
            }

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
