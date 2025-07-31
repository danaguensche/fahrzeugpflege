<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string',
            'car_id' => 'nullable|exists:cars,id',
            'job_id' => 'nullable|exists:jobs,id',
        ]);

        $path = $request->file('image')->store('images', 'public');

        $image = Image::create([
            'user_id' => auth()->id(),
            'car_id' => $request->car_id,
            'job_id' => $request->job_id,
            'path' => $path,
            'description' => $request->description,
        ]);

        return response()->json($image, 201);
    }

    public function index(Request $request)
    {
        $query = Image::with(['user:id,name', 'job:id,name', 'car:id,name']);

        if ($request->has('car_id')) {
            $query->where('car_id', $request->car_id);
        }

        if ($request->has('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        $user = auth()->user();

        if ($user->role === 'trainee') {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->get());
    }

    public function destroy(Image $image)
    {
        $user = auth()->user();

        if ($user->id !== $image->user_id && !in_array($user->role, ['trainer', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }
}