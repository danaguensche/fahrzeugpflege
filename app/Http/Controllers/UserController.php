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
    public function index(Request $request)
    {
        try {
            $sortBy = $request->input('sortBy', 'id');
            // The frontend sends sortDesc=true for ASC order and sortDesc=false for DESC order.
            $sortOrder = $request->input('sortDesc', 'false') === 'true' ? 'asc' : 'desc';
            $itemsPerPage = $request->input('itemsPerPage', 10);

            $users = User::orderBy($sortBy, $sortOrder)
                         ->paginate($itemsPerPage);

            return response()->json([
                'items' => UserResource::collection($users->items()),
                'total' => $users->total(),
            ]);
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
    public function update(Request $request, $id = null)
    {
        try {
            if ($id) {
                // An admin or trainer is updating a specific user.
                $user = User::findOrFail($id);
            } else {
                // A user is updating their own profile.
                $user = Auth::user();
            }

            if (!$user) {
                return response()->json(['message' => 'Benutzer nicht gefunden'], 404);
            }

            $validatedData = $request->validate([
                'firstname' => 'sometimes|string|max:255',
                'lastname' => 'sometimes|string|max:255',
                'phonenumber' => 'nullable|string|max:255',
                'addressline' => 'nullable|string|max:255',
                'postalcode' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'role' => 'sometimes|in:admin,trainer,trainee',
                'password' => 'nullable|string|min:8',
            ]);

            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);

            return new UserResource($user);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validierungsfehler',
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage() . ' Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Fehler beim Aktualisieren des Benutzers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $searchQuery = $request->input('query');

        $users = User::where(function ($queryBuilder) use ($searchQuery) {
            $queryBuilder->where('firstname', 'like', '%' . $searchQuery . '%')
                         ->orWhere('lastname', 'like', '%' . $searchQuery . '%')
                         ->orWhere('email', 'like', '%' . $searchQuery . '%');
        })->get();

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * Get all users with the 'trainee' role.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getTrainees()
    {
        $trainees = User::where('role', 'trainee')->get();
        return UserResource::collection($trainees);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Benutzer erfolgreich gelöscht']);
    }

    /**
     * Remove multiple users from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids');
        User::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Benutzer erfolgreich gelöscht']);
    }
}