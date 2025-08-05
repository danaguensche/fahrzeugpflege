<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Http\Resources\JobResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class JobDetailsController extends Controller
{
    public function details($id)
    {
        Log::info('JobDetailsController@details called', ['id' => $id, 'user_id' => auth()->check() ? auth()->id() : null, 'authenticated' => auth()->check()]);
        $job = Job::with(['customer', 'car', 'services', 'trainee', 'images'])->find($id);

        if ($job !== null) {
            return new JobResource($job);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Job nicht gefunden',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $job = Job::findOrFail($id);
            $user = auth()->user();

            if ($user && $user->role === 'trainee') {
                // Trainee can only update status for their own jobs
                if ($job->user_id !== $user->id) {
                    abort(403, 'Unauthorized action. You can only update your own jobs.');
                }

                // Validate that only 'status' is being updated
                $allowedFields = ['status'];
                $requestFields = array_keys($request->all());
                $diff = array_diff($requestFields, $allowedFields);

                if (!empty($diff)) {
                    abort(403, 'Unauthorized action. Trainees can only update job status.');
                }

                $validatedData = $request->validate([
                    'status' => 'required|string',
                ]);

                $job->update($validatedData);

                Log::info('Job trainee_id after update', ['trainee_id' => $job->trainee_id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Jobdaten erfolgreich aktualisiert',
                    'data' => new JobResource($job->fresh(['customer', 'car', 'services', 'trainee', 'images']))
                ]);

            } else { // Admin or Trainer
                $validationRules = [
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'scheduled_at' => 'nullable|date',
                    'status' => 'required|string|max:255',
                    'trainee_id' => 'nullable|exists:users,id',
                    'customer_id' => 'nullable|exists:customers,id',
                    'car_id' => 'nullable|exists:cars,id',
                    'services' => 'nullable|array',
                    'services.*' => 'exists:services,id',
                ];

                $validatedData = $request->validate($validationRules);

                $job->update($validatedData);

                Log::info('Job trainee_id after update', ['trainee_id' => $job->trainee_id]);

                if (isset($validatedData['services'])) {
                    $job->services()->sync($validatedData['services']);
                }
                Log::info('JobController@update payload', $request->all());


                return response()->json([
                    'success' => true,
                    'message' => 'Jobdaten erfolgreich aktualisiert',
                    'data' => new JobResource($job->fresh(['customer', 'car', 'services', 'trainee', 'images']))
                ]);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Job not found for update', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Job nicht gefunden'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error during job update', [
                'id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating job: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteImage($imageId)
    {
        try {
            Log::info("Attempting to delete image", [
                'imageId' => $imageId,
                'imageId_type' => gettype($imageId),
                'request_uri' => request()->fullUrl(),
                'request_method' => request()->method()
            ]);

            // Check if imageId is null or empty
            if (is_null($imageId) || $imageId === '' || $imageId === 'null') {
                Log::error("Invalid image ID provided for deletion", [
                    'imageId' => $imageId
                ]);
                return response()->json([
                    'error' => 'Bild-ID nicht gefunden',
                    'message' => 'Ungültige Bild-ID'
                ], 400);
            }

            $image = \App\Models\Image::findOrFail($imageId);

            Log::info("Found image to delete", [
                'image_id' => $image->id,
                'image_path' => $image->path
            ]);

            // Delete file from storage
            $imagePath = str_replace('storage/', '', $image->path);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
                Log::info("File deleted from storage", ['path' => $imagePath]);
            } else {
                Log::warning("File not found in storage", ['path' => $imagePath]);
            }

            // Delete record from database
            $image->delete();

            Log::info("Image successfully deleted from database");

            return response()->json([
                'message' => 'Bild erfolgreich gelöscht',
                'success' => true
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Image not found for deletion", [
                'imageId' => $imageId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Bild nicht gefunden'], 404);
        } catch (\Exception $e) {
            Log::error('Fehler beim Löschen des Bildes: ' . $e->getMessage(), [
                'imageId' => $imageId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Fehler beim Löschen des Bildes: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Replace specific image
     */
    public function replaceImage(Request $request, $id, $imageId)
    {
        try {
            $id = str_replace('+', ' ', $id);
            
            Log::info("Attempting to replace image", [
                'id' => $id,
                'imageId' => $imageId,
                'imageId_type' => gettype($imageId)
            ]);

            // Check if imageId is null or empty
            if (is_null($imageId) || $imageId === '' || $imageId === 'null') {
                Log::error("Invalid image ID provided", [
                    'id' => $id,
                    'imageId' => $imageId
                ]);
                return response()->json([
                    'error' => 'Bild-ID nicht gefunden',
                    'message' => 'Ungültige Bild-ID'
                ], 400);
            }

            $job = Job::where('id', $id)->firstOrFail();
            
            // Log all images for debugging
            Log::info("Job images", [
                'job_id' => $job->id,
                'images' => $job->images()->select('id', 'path')->get()->toArray()
            ]);

            $image = $job->images()->where('id', $imageId)->first();

            if (!$image) {
                Log::error("Image not found", [
                    'id' => $id,
                    'imageId' => $imageId,
                    'available_images' => $job->images()->pluck('id')->toArray()
                ]);
                return response()->json([
                    'error' => 'Bild nicht gefunden',
                    'message' => 'Das angegebene Bild existiert nicht'
                ], 404);
            }

            $validatedData = $request->validate([
                'image' => 'required|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            Log::info("Found image to replace", [
                'image_id' => $image->id,
                'old_path' => $image->path
            ]);

            if ($request->hasFile('image')) {
                // Delete old file
                $oldImagePath = str_replace('storage/', '', $image->path);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                    Log::info("Old file deleted", ['path' => $oldImagePath]);
                }

                // Store new file
                $newPath = $request->file('image')->store('jobs', 'public');
                $image->update(['path' => $newPath]);
                
                Log::info("New file stored", ['new_path' => $newPath]);
            }

            $job->load('images');

            return response()->json([
                'message' => 'Bild erfolgreich ersetzt',
                'job' => new JobResource($job),
                'success' => true
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error during image replacement", [
                'errors' => $e->errors(),
                'id' => $id,
                'imageId' => $imageId
            ]);
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Job not found for replacement", [
                'id' => $id,
                'imageId' => $imageId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Fahrzeug nicht gefunden'], 404);
        } catch (\Exception $e) {
            Log::error('Fehler beim Ersetzen des Bildes: ' . $e->getMessage(), [
                'id' => $id,
                'imageId' => $imageId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Fehler beim Ersetzen des Bildes: ' . $e->getMessage()], 500);
        }
    }
}
