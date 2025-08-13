<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Image upload request received', $request->all());

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string',
            'car_id' => 'nullable|exists:cars,id',
            'job_id' => 'nullable|exists:auftraege,id',
        ]);

        if ($validator->fails()) {
            Log::error('Image upload validation failed', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }

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

    // In ImageController oder einem passenden Controller
    public function assignToCar(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:images,id',
            'car_id' => 'required|exists:cars,id'
        ]);

        // Einfach die car_id in der images Tabelle aktualisieren
        DB::table('images')
            ->whereIn('id', $request->image_ids)
            ->update(['car_id' => $request->car_id]);

        return response()->json([
            'message' => 'Images successfully assigned to car',
            'updated_count' => count($request->image_ids)
        ]);
    }
}
