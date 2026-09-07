<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Job;
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


    public function destroy($imageId)
    {
        try {

            $imageFromDB = DB::table('images')->where('id', $imageId)->first();

            if ($imageFromDB) {

                // Lösche vom Storage
                $imagePath = str_replace('storage/', '', $imageFromDB->path);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                // Lösche aus DB
                DB::table('images')->where('id', $imageId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Bild erfolgreich gelöscht'
                ]);
            }

            // 2. Versuche image_reports
            $jobImage = DB::table('image_reports')->where('id', $imageId)->first();

            if ($jobImage) {

                $imagePath = str_replace('storage/', '', $jobImage->path);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                DB::table('image_reports')->where('id', $imageId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Bild erfolgreich gelöscht'
                ]);
            }

            Log::warning('Image not found');
            return response()->json([
                'success' => false,
                'message' => 'Bild nicht gefunden'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting image:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen'
            ], 500);
        }
    }



    public function assignToCar(Request $request, $jobId)
    {
        $validated = $request->validate([
            'imageIds' => 'required|array',
            'imageIds.*' => 'exists:images,id',
            'car_id' => 'required|exists:cars,id'
        ]);

        $job = Job::findOrFail($jobId);

        // Nur Bilder updaten, die zum Job gehören
        $job->images()
            ->whereIn('id', $validated['imageIds'])
            ->update(['car_id' => $validated['car_id']]);

        return response()->json([
            'message' => 'Bilder erfolgreich dem Fahrzeug zugewiesen',
            'updated_count' => $job->images()->whereIn('id', $validated['imageIds'])->count()
        ]);
    }
}
