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

    public function index()
    {
        return new CustomerCollection(Customer::paginate());
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }
}
