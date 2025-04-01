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
            'firstname'   => 'required|string|max:255',
            'lastname'    => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:employees',
            'phonenumber' => 'required|string|max:20',
            'addressline' => 'required|string|max:255',
            'postalcode'  => 'required|string|max:10',
            'city'        => 'required|string|max:255',
            'password'    => 'required|string|min:8',
        ]);

        $employee = Employee::create($validatedData);

        return response()->json([
            'message'  => 'Mitarbeiter erfolgreich erstellt',
            'employee' => $employee
        ], 201);
    }
    public function show($id)
    {
        $user = Auth::user();

        if (!$user || $user->id != $id) {
            return response()->json(['message' => 'Nicht autorisiert'], 401);
        }

        $employee = Employee::findOrFail($id);
        return response()->json(['employee' => $employee], 200);
    }

    public function index()
    {
        $employees = Employee::all();

        return response()->json(['employees' => $employees], 200);
    }
}
