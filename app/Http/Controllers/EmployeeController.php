<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Nicht autorisiert'], 401);
        }

        $employee = Employee::create([
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'phonenumber' => $user->phonenumber,
            'addressline' => $user->addressline,
            'postalcode' => $user->postalcode,
            'city' => $user->city,
        ]);

        return response()->json(['message' => 'Mitarbeiterdaten erfolgreich gespeichert', 'employee' => $employee], 201);
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
