<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class AuthController extends Controller
{
    
    public function logout(Request $request): JsonResponse

    {
        // Auth::logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        // return response()->json(['message' => 'Sie wurden erfolgreich abgemeldet.']);

        //Vorher Session basiert, jetzt Token basiert (Sanctum)
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Sie wurden erfolgreich abgemeldet.']);
    }
        
    public function loginPost(LoginUserRequest $request): JsonResponse
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json(['message' => 'Ungültige Anmeldedaten'], 401);
        }

        $user = User::where('username', $request->username)->firstOrFail();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'username' => $user->username,
                'role' => $user->role,
            ],
            'token' => $token,
            'redirect' => '/dashboard'
        ]);
    }


    public function signupPost(Request $request)
    {
        $request->validate([
            "firstname"   => "required|string|max:30",
            "lastname"    => "required|string|max:30",
            "username"    => "required|string|max:255|unique:users,username|exists:allowed_usernames,username",
            "password"    => "required|string|min:8|confirmed",
            "phoneNumber" => "nullable|string|max:20",
            "addressLine" => "nullable|string|max:255",
            "postalCode"  => "nullable|string|max:10",
            "city"        => "nullable|string|max:255",
        ]);
    
        $allowed = DB::table('allowed_usernames')
            ->where('username', $request->username)
            ->where('claimed', false)
            ->first();
    
        if (! $allowed) {
            return response()->json(['message' => 'Dieser Benutzername ist nicht freigegeben oder wurde bereits verwendet.'], 403);
        }
    
        // User anlegen
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'role'      => $allowed->role,
        ]);
    
        // markiere den Username als verbraucht
        DB::table('allowed_usernames')->where('username', $request->username)->update(['claimed' => true]);
    
        $token = $user->createToken('authToken')->plainTextToken;
    
        return response()->json([
            'success' => true,
            'user'    => $user,
            'token'   => $token,
        ]);
    }
}
