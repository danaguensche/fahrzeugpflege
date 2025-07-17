<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Store a newly created user in the database.
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create($validatedData);
        return response()->json([
            'message' => 'Benutzer erfolgreich erstellt',
            'user' => new UserResource($user)
        ], 201);
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Benutzer nicht gefunden'], 404);
        }
        return new UserResource($user);
    }

    /**
     * Get all users.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try {
            return UserResource::collection(User::all());
        } catch (\Exception $e) {
            Log::error('Error in index method: ' . $e->getMessage());
            return response()->json([
                'message' => 'Fehler beim Abrufen der Benutzer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\UserResource
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            // Überprüfen, ob der Benutzer authentifiziert ist
            if (!$user) {
                return response()->json([
                    'message' => 'Benutzer nicht authentifiziert',
                ], 401);
            }

            // Loggen der Benutzerklasse für Debugging-Zwecke
            Log::info('User class: ' . get_class($user));

            $validatedData = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'phonenumber' => 'nullable|string|max:255',
                'addressline' => 'nullable|string|max:255',
                'postalcode' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'password' => 'nullable|string|min:8',
            ]);

            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            // Zuerst versuchen wir es mit direktem Update
            try {
                $result = $user->update($validatedData);
                Log::info('Update result: ' . ($result ? 'success' : 'failure'));

                if (!$result) {
                    // Wenn update() fehlschlägt, versuchen wir es mit fill() und save()
                    $user->fill($validatedData);
                    $saveResult = $user->save();
                    Log::info('Save result after fill: ' . ($saveResult ? 'success' : 'failure'));

                    if (!$saveResult) {
                        throw new \Exception('Sowohl update() als auch fill()->save() sind fehlgeschlagen');
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error updating user: ' . $e->getMessage());
                throw $e;
            }

            // Frischen Benutzer aus der Datenbank abrufen, um zu überprüfen, ob die Änderungen gespeichert wurden
            $refreshedUser = User::find($user->id);
            return new UserResource($refreshedUser);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validierungsfehler',
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fehler beim Aktualisieren des Benutzers',
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }
}
