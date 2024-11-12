<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

    function loginPost(Request $request)
    {
        $request->validate([
            "email" => "required",
            "password" => "required"
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['success' => true, 'redirect' => route('dashboard')]);
        } else {
            return response()->json(['success' => false, 'message' => 'Login fehlgeschlagen. Bitte überprüfen Sie Ihre Eingaben.'], 401);
        }
    }

    function signupPost(Request $request)
    {
        $request->validate([
            "firstname" => "required|string|max:255",
            "lastname" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
        ]);

        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            return response()->json(['success' => true, 'message' => 'User created successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to create user'], 500);
        }
    }
}
