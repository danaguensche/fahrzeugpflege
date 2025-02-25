<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        $employee = Employee::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'addressline' => $request->addressline,
            'postalcode' => $request->postalcode,
            'city' => $request->city,
            'password' => bcrypt($request->password), 
        ]);

        return response()->json(['message' => 'Mitarbeiter erfolgreich erstellt', 'employee' => $employee], 201);
    }
}
