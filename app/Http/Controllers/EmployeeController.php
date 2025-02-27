<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees',
            'phonenumber' => 'required|string|max:20',
            'addressline' => 'required|string|max:255',
            'postalcode' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        $employee = Employee::create([
            'firstname' => $validatedData['firstname'],
            'lastname' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'phonenumber' => $validatedData['phonenumber'],
            'addressline' => $validatedData['addressline'],
            'postalcode' => $validatedData['postalcode'],
            'city' => $validatedData['city'],
            'password' => bcrypt($validatedData['password']),
        ]);

        return response()->json(['message' => 'Mitarbeiter erfolgreich erstellt', 'employee' => $employee], 201);
    }


    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Nicht autorisiert'], 401);
        }

        $employee = Employee::find($user->id);

        if (!$employee) {
            return response()->json(['message' => 'Mitarbeiter nicht gefunden'], 404);
        }

        return response()->json(['employee' => $employee], 200);
    }

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Nicht autorisiert'], 401);
        }

        $employee = Employee::find($user->id);

        if (!$employee) {
            return response()->json(['message' => 'Mitarbeiter nicht gefunden'], 404);
        }

        return response()->json(['employee' => $employee], 200);
    }
}
