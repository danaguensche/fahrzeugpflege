<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CarDetailsController extends CarController
{
    public function details($kennzeichen)
    {
        $kennzeichen = str_replace('+', ' ', $kennzeichen);
        $car = Car::with(['images', 'customer'])->where('Kennzeichen', $kennzeichen)->first();

        if ($car !== null) {
            return new CarResource($car);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Fahrzeug nicht gefunden',
            ], 404);
        }
    }

    public function update(Request $request, $kennzeichen)
    {
        try {
            $kennzeichen = urldecode($kennzeichen);
            $kennzeichen = str_replace('+', ' ', $kennzeichen);
            $car = Car::where('Kennzeichen', $kennzeichen)->firstOrFail();

            // Prepare validation rules
            $validationRules = [
                'Kennzeichen' => 'required|string',
                'Fahrzeugklasse' => 'nullable|integer',
                'Automarke' => 'nullable|string',
                'Typ' => 'nullable|string',
                'Farbe' => 'nullable|string',
                'Sonstiges' => 'nullable|string',
            ];

            // Handle customer_id validation - allow null/0 or valid customer ID
            $customerId = $request->input('customer_id');
            
            // If customer_id is provided and not null/0, validate it exists
            if ($customerId !== null && $customerId !== 0 && $customerId !== '0' && $customerId !== '') {
                $validationRules['customer_id'] = 'exists:customers,id';
            } else {
                // Allow null/0 values for customer_id
                $validationRules['customer_id'] = 'nullable';
            }

            $validatedData = $request->validate($validationRules);

            // Handle customer_id assignment
            if ($request->has('customer_id')) {
                $customerIdValue = $request->input('customer_id');
                
                Log::info('Car assignment request received', [
                    'Kennzeichen' => $kennzeichen,
                    'customer_id' => $customerIdValue,
                    'customer_id_type' => gettype($customerIdValue)
                ]);

                // Convert to integer, but allow null/0
                if ($customerIdValue === null || $customerIdValue === '' || $customerIdValue === 0 || $customerIdValue === '0') {
                    $validatedData['customer_id'] = null;
                } else {
                    $validatedData['customer_id'] = (int)$customerIdValue;
                }

                Log::info('Customer ID processed', [
                    'original' => $customerIdValue,
                    'processed' => $validatedData['customer_id']
                ]);
            }

            $updated = $car->update($validatedData);

            if (!$updated) {
                throw new \Exception('Failed to update car data');
            }

            Log::info('Car assignment successful', [
                'Kennzeichen' => $kennzeichen,
                'customer_id' => $car->customer_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fahrzeugdaten aktualisiert',
                'data' => new CarResource($car->fresh(['images', 'customer']))
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Car not found for update', [
                'kennzeichen' => $kennzeichen,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Fahrzeug nicht gefunden'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error during car update', [
                'kennzeichen' => $kennzeichen,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating car: ' . $e->getMessage(), [
                'kennzeichen' => $kennzeichen,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload images to existing car
     */
    public function uploadImages(Request $request, $kennzeichen)
    {
        try {
            $kennzeichen = str_replace('+', ' ', $kennzeichen);
            $car = Car::where('Kennzeichen', $kennzeichen)->firstOrFail();

            $validatedData = $request->validate([
                'images' => 'required|array',
                'images.*' => 'required|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $uploadedImages = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('cars', 'public');
                    $carImage = $car->images()->create(['path' => $path]);
                    $uploadedImages[] = $carImage;
                    
                    Log::info("Image uploaded", [
                        'image_id' => $carImage->id,
                        'path' => $carImage->path,
                        'car_id' => $car->id
                    ]);
                }
            }

            // Reload car with fresh images
            $car = $car->fresh(['images']);

            Log::info("Final car images after upload", [
                'car_id' => $car->id,
                'images' => $car->images->map(function($img) {
                    return [
                        'id' => $img->id,
                        'path' => $img->path
                    ];
                })
            ]);

            return response()->json([
                'message' => 'Bilder erfolgreich hochgeladen',
                'car' => new CarResource($car),
                'uploaded_images' => $uploadedImages,
                'success' => true
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error during image upload", [
                'errors' => $e->errors(),
                'kennzeichen' => $kennzeichen
            ]);
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Fehler beim Hochladen der Bilder: ' . $e->getMessage(), [
                'kennzeichen' => $kennzeichen,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Fehler beim Hochladen der Bilder'], 500);
        }
    }

    /**
     * Delete specific image
     */
    public function deleteImage($kennzeichen, $imageId)
    {
        try {
            $kennzeichen = str_replace('+', ' ', $kennzeichen);
            
            Log::info("Attempting to delete image", [
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId,
                'imageId_type' => gettype($imageId)
            ]);

            // Check if imageId is null or empty
            if (is_null($imageId) || $imageId === '' || $imageId === 'null') {
                Log::error("Invalid image ID provided for deletion", [
                    'kennzeichen' => $kennzeichen,
                    'imageId' => $imageId
                ]);
                return response()->json([
                    'error' => 'Bild-ID nicht gefunden',
                    'message' => 'Ungültige Bild-ID'
                ], 400);
            }

            $car = Car::where('Kennzeichen', $kennzeichen)->firstOrFail();
            
            // Log all images for debugging
            Log::info("Car images for deletion", [
                'car_id' => $car->id,
                'images' => $car->images()->select('id', 'path')->get()->toArray()
            ]);

            $image = $car->images()->where('id', $imageId)->first();

            if (!$image) {
                Log::error("Image not found for deletion", [
                    'kennzeichen' => $kennzeichen,
                    'imageId' => $imageId,
                    'available_images' => $car->images()->pluck('id')->toArray()
                ]);
                return response()->json([
                    'error' => 'Bild nicht gefunden',
                    'message' => 'Das angegebene Bild existiert nicht'
                ], 404);
            }

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
            Log::error("Car not found for deletion", [
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Fahrzeug nicht gefunden'], 404);
        } catch (\Exception $e) {
            Log::error('Fehler beim Löschen des Bildes: ' . $e->getMessage(), [
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Fehler beim Löschen des Bildes: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Replace specific image
     */
    public function replaceImage(Request $request, $kennzeichen, $imageId)
    {
        try {
            $kennzeichen = str_replace('+', ' ', $kennzeichen);
            
            Log::info("Attempting to replace image", [
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId,
                'imageId_type' => gettype($imageId)
            ]);

            // Check if imageId is null or empty
            if (is_null($imageId) || $imageId === '' || $imageId === 'null') {
                Log::error("Invalid image ID provided", [
                    'kennzeichen' => $kennzeichen,
                    'imageId' => $imageId
                ]);
                return response()->json([
                    'error' => 'Bild-ID nicht gefunden',
                    'message' => 'Ungültige Bild-ID'
                ], 400);
            }

            $car = Car::where('Kennzeichen', $kennzeichen)->firstOrFail();
            
            // Log all images for debugging
            Log::info("Car images", [
                'car_id' => $car->id,
                'images' => $car->images()->select('id', 'path')->get()->toArray()
            ]);

            $image = $car->images()->where('id', $imageId)->first();

            if (!$image) {
                Log::error("Image not found", [
                    'kennzeichen' => $kennzeichen,
                    'imageId' => $imageId,
                    'available_images' => $car->images()->pluck('id')->toArray()
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
                $newPath = $request->file('image')->store('cars', 'public');
                $image->update(['path' => $newPath]);
                
                Log::info("New file stored", ['new_path' => $newPath]);
            }

            $car->load('images');

            return response()->json([
                'message' => 'Bild erfolgreich ersetzt',
                'car' => new CarResource($car),
                'success' => true
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error during image replacement", [
                'errors' => $e->errors(),
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId
            ]);
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Car not found for replacement", [
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Fahrzeug nicht gefunden'], 404);
        } catch (\Exception $e) {
            Log::error('Fehler beim Ersetzen des Bildes: ' . $e->getMessage(), [
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Fehler beim Ersetzen des Bildes: ' . $e->getMessage()], 500);
        }
    }
}