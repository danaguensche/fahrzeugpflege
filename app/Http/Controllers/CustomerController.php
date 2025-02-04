<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerCollection;

class CustomerController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'company' => 'nullable|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phonenumber' => 'required|string',
            'addressline' => 'required|string',
            'postalcode' => 'required|string',
            'city' => 'required|string',
        ]);

        $customer = new Customer();
        $customer->company = $request->company;
        $customer->firstname = $request->firstname;
        $customer->lastname = $request->lastname;
        $customer->email = $request->email;
        $customer->phonenumber = $request->phonenumber;
        $customer->addressline = $request->addressline;
        $customer->postalcode = $request->postalcode;
        $customer->city = $request->city;

        if ($customer->save()) {
            return response()->json(['success' => true, 'message' => 'Kunde wurde hinzugefügt.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Beim Erstellen des Kunden ist ein Fehler aufgetreten.'], 500);
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
}
