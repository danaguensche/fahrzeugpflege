<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'company' => 'nullable|string|max:255',
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phonenumber' => 'required|string',
                'addressline' => 'required|string',
                'postalcode' => 'required|string',
                'city' => 'required|string',
            ]);

            $customer = Customer::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kunde wurde hinzugefügt.',
                'data' => $customer
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ein Fehler ist aufgetreten',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 20);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = $request->input('sortDesc', 'false') === 'true';

        $query = Customer::query();

        if ($sortBy) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        $total = $query->count();
        $customers = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'items' => CustomerResource::collection($customers),
            'total' => $total,
        ]);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Kunde wurde gelöscht.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Kunde nicht gefunden.'], 404);
        }
    }

    public function destroyMultiple(Request $request)
    {
        Customer::destroy($request->ids);
        return response()->json(null, 204);
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::where('id', $id)->firstOrFail();

            $validatedData = $request->validate([
                'company' => 'nullable|string|max:255',
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email,' . $id,
                'phonenumber' => 'required|string',
                'addressline' => 'required|string',
                'postalcode' => 'required|string',
                'city' => 'required|string',
            ]);

            $customer->update([
                'company' => $validatedData['company'] ?? null,
                'firstname' => $validatedData['firstname'],
                'lastname' => $validatedData['lastname'],
                'email' => $validatedData['email'],
                'phonenumber' => $validatedData['phonenumber'],
                'addressline' => $validatedData['addressline'],
                'postalcode' => $validatedData['postalcode'],
                'city' => $validatedData['city'],
            ]);

            return response()->json([
                'message' => 'Kunde erfolgreich aktualisiert',
                'customer' => new CustomerResource($customer)
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Aktualisieren des Kunden: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler beim Aktualisieren des Kunden'], 500);
        }
    }
}
