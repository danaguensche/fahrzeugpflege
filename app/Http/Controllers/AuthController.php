<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;


class AuthController extends Controller
{
    public function login()
    {
        return view("auth.login");
    }

    public function signup()
    {
        return view("auth.signup");
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Sie wurden erfolgreich abgemeldet.']);
    }

    function loginPost(Request $request)
    {
        $request->validate([
            "email" => "required",
            "password" => "required"
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'success' => true,
                'token' => $token,
                'user_id' => $user->id,
                'redirect' => '/dashboard',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Ungültige Anmeldedaten',
        ], 401);
    }

    public function signupPost(Request $request)
    {
        $request->validate([
            "firstname" => "required|string|max:30",
            "lastname" => "required|string|max:30",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
        ]);

        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            //Neuer Mitarbeiter wird erstellt sobald er sich registriert
            $employee = new Employee();
            //Verknüpfe User mit Employee
            $employee->user_id = $user->id;
            $employee->id = $user->id;
            $employee->firstname = $user->firstname;
            $employee->lastname = $user->lastname;
            $employee->email = $user->email;
            $employee->password = $user->password;
            $employee->save();

            return response()->json(['success' => true, 'message' => 'Benutzer und Mitarbeiter wurden erfolgreich erstellt.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Beim Erstellen des Benutzers ist ein Fehler aufgetreten.'], 500);
        }

        return $this->succes([
            'user' => $user,
            'token' => $user->createToken('API Token of'.$user->firstname.$user->lastname)->plainTextToken
        ]);
    }
}
