<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    function loginPost(Request $request) {}

    function signupPost(Request $request)
    {
        $request->validate([
            //google validation
            "firstname" => "required",
            "lastname" => "required",
            "email" => "required",
            "password" => "required",
            "password-repeat" => "required"
        ]);

        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if($user->save()){
            return redirect(route("login"))->with("success", "User created successfully");
        }

        return redirect(route("login"))->with("error", "failed to create User");
    }
}
