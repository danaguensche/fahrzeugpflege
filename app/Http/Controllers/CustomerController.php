<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use function activity;

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
                'phonenumber' => 'nullable|string|max:255',
                'addressline' => 'nullable|string|max:255',
                'postalcode' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'notes' => 'nullable|text',
            ]);

            $customer = Customer::create($validated);

            activity()
            ->causedBy(auth()->user())
            ->withProperties(['customer_id' => $customer->id])
            ->log('Kunde erstellt: ' . $customer->firstname . ' ' . $customer->lastname . ' von' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

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
        $maxPerPage = 100;
        $perPage = min((int) request()->input('itemsPerPage', 20), $maxPerPage);
        $page = max((int) request()->input('page', 1), 1);
        $sortBy = request()->input('sortBy', 'id');
        $sortDesc = filter_var(request()->input('sortDesc', 'false'), FILTER_VALIDATE_BOOLEAN);

        $allowedSortFields = ['id', 'firstname', 'lastname', 'email', 'phonenumber', 'company', 'addressline', 'postalcode', 'city'];

        $query = Customer::query();

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        $total = $query->count();
        $customers = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'items' => CustomerResource::collection($customers),
            'total' => $total,
        ]);
    }

    /**
     * NEW METHOD: Customer Search
     */
    public function search(Request $request)
    {
        try {
            $maxPerPage = 100;
            $query = $request->input('query', '');
            $perPage = min((int) request()->input('itemsPerPage', 20), $maxPerPage);
            $page = $request->input('page', 1);
            $sortBy = $request->input('sortBy', 'id');
            $sortDesc = filter_var(request()->input('sortDesc', false), FILTER_VALIDATE_BOOLEAN);

            $allowedSortFields = ['id', 'firstname', 'lastname', 'email', 'phonenumber', 'company', 'addressline', 'postalcode', 'city'];

            // If the query is empty, return an empty result
            if (empty(trim($query))) {
                return response()->json([
                    'items' => [],
                    'total' => 0,
                ]);
            }

            $queryBuilder = Customer::query();

            // Search by all main fields
            $queryBuilder->where(function ($q) use ($query) {
                $searchTerm = '%' . $query . '%';
                $q->where('firstname', 'like', $searchTerm)
                    ->orWhere('lastname', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phonenumber', 'like', $searchTerm)
                    ->orWhere('company', 'like', $searchTerm)
                    ->orWhere('addressline', 'like', $searchTerm)
                    ->orWhere('postalcode', 'like', $searchTerm)
                    ->orWhere('city', 'like', $searchTerm);

                // Search by ID if the query is numeric
                if (is_numeric($query)) {
                    $q->orWhere('id', '=', (int)$query);
                }
            });

            // Applying sorting
            if (in_array($sortBy, $allowedSortFields)) {
                $queryBuilder->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
            }

            $total = $queryBuilder->count();
            $customers = $queryBuilder->skip(($page - 1) * $perPage)->take($perPage)->get();

            return response()->json([
                'items' => CustomerResource::collection($customers),
                'total' => $total,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in customer search: ' . $e->getMessage());
            return response()->json([
                'items' => [],
                'total' => 0,
                'error' => 'Error during search'
            ], 500);
        }
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::find($id);
            if ($customer) {
                $customer->delete();

                activity()
            ->causedBy(auth()->user())
            ->withProperties(['customer_id' => $customer->id])
            ->log('Kunde gelöscht: ' . $customer->firstname . ' ' . $customer->lastname . ' von' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

                return response()->json([
                    'success' => true,
                    'message' => 'Kunde wurde gelöscht.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Kunde nicht gefunden.'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Fehler beim Löschen des Kunden: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen des Kunden'
            ], 500);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:customers,id'
            ]);

            Customer::destroy($validated['ids']);

            activity()
            ->causedBy(auth()->user())
            ->withProperties(['customer_ids' => $validated['ids']])
            ->log('Mehrere Kunden gelöscht von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            return response()->json([
                'success' => true,
                'message' => 'Kunden wurden erfolgreich gelöscht.'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Löschen mehrerer Kunden: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen der Kunden'
            ], 500);
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
                'email' => 'required|email|unique:customers,email,' . $id,
                'phonenumber' => 'nullable|string|max:255',
                'addressline' => 'nullable|string|max:255',
                'postalcode' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'notes' => 'nullable|text',
            ]);

            // CORRECTED: send all validated data
            $customer->update($validatedData);

            activity()
            ->causedBy(auth()->user())
            ->withProperties(['customer_id' => $customer->id])
            ->log('Kunde bearbeitet: ' . $customer->firstname . ' ' . $customer->lastname . ' von' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            return response()->json([
                'success' => true,
                'message' => 'Kunde erfolgreich aktualisiert',
                'customer' => new CustomerResource($customer)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kunde nicht gefunden'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Aktualisieren des Kunden: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren des Kunden'
            ], 500);
        }
    }

    public function removeCarFromCustomer($customerId, $carId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            $car = $customer->cars()->findOrFail($carId);

            // Assuming a one-to-many relationship where Car has a customer_id
            $car->customer_id = null;
            $car->save();

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['customer_id' => $customer->id, 'car_id' => $car->id])
                ->log('Fahrzeug entfernt: ' . $car->kennzeichen . ' von Kunde ' . $customer->firstname . ' ' . $customer->lastname . ' von ' . auth()->user()->firstname . ' ' . auth()->user()->lastname);

            return response()->json(['success' => true, 'message' => 'Fahrzeug erfolgreich vom Kunden entfernt.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Fehler beim Entfernen des Fahrzeugs.'], 500);
        }
    }

    public function getCurrentMonthCustomers()
    {
        try {
            $customers = Customer::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'count' => $customers->count(),
                'data' => CustomerResource::collection($customers),
                'month' => Carbon::now()->format('F Y') 
            ]);
        } catch (\Exception $e) {
            Log::error('Fehler beim Abrufen der Kunden des aktuellen Monats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Abrufen der Kunden',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
