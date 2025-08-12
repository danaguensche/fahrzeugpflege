<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Image; 
use Exception;

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
                throw new Exception('Failed to update car data');
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
        } catch (Exception $e) {
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

            // Debug: Log system info for server differences
            Log::info('Upload attempt - System info', [
                'kennzeichen' => $kennzeichen,
                'php_version' => PHP_VERSION,
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit'),
                'file_uploads' => ini_get('file_uploads'),
                'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: sys_get_temp_dir(),
                'storage_path' => storage_path('app/public/cars'),
                'storage_exists' => is_dir(storage_path('app/public/cars')),
                'storage_writable' => is_writable(storage_path('app/public/')),
            ]);

            // Ensure storage directory exists
            $storagePath = storage_path('app/public/cars');
            if (!is_dir($storagePath)) {
                if (!mkdir($storagePath, 0755, true)) {
                    throw new Exception('Konnte Storage-Verzeichnis nicht erstellen: ' . $storagePath);
                }
                Log::info('Created storage directory', ['path' => $storagePath]);
            }

            $validatedData = $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'required|file|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            Log::info('Validation passed', [
                'files_count' => count($request->file('images', [])),
                'has_files' => $request->hasFile('images')
            ]);

            $uploadedImages = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    // Additional file validation
                    if (!$image) {
                        Log::warning("File at index {$index} is null");
                        continue;
                    }

                    if (!$image->isValid()) {
                        $error = $image->getError();
                        Log::error("File at index {$index} is not valid", [
                            'error_code' => $error,
                            'error_message' => $this->getUploadErrorMessage($error),
                            'original_name' => $image->getClientOriginalName(),
                            'size' => $image->getSize(),
                        ]);
                        continue;
                    }

                    Log::info("Processing file {$index}", [
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'temp_path' => $image->getRealPath(),
                        'is_valid' => $image->isValid(),
                        'error' => $image->getError()
                    ]);

                    try {
                        // Generate unique filename
                        $extension = $image->getClientOriginalExtension();
                        $filename = uniqid('car_' . $car->id . '_') . '.' . $extension;
                        
                        // Store with custom filename to avoid path issues
                        $path = $image->storeAs('cars', $filename, 'public');
                        
                        if (!$path) {
                            throw new Exception('Datei konnte nicht gespeichert werden - store() gab false zurück');
                        }

                        $carImage = $car->images()->create(['path' => $path]);
                        $uploadedImages[] = $carImage;
                        
                        Log::info("Image uploaded successfully", [
                            'index' => $index,
                            'image_id' => $carImage->id,
                            'path' => $carImage->path,
                            'car_id' => $car->id,
                            'filename' => $filename,
                            'full_path' => storage_path('app/public/' . $path)
                        ]);

                    } catch (Exception $e) {
                        Log::error("Failed to store file {$index}", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'original_name' => $image->getClientOriginalName(),
                        ]);
                        
                        // Continue with other files instead of failing completely
                        continue;
                    }
                }
            } else {
                Log::warning('No files found in request', [
                    'request_files' => $request->files->all(),
                    'request_all' => $request->all()
                ]);
            }

            if (empty($uploadedImages)) {
                throw new Exception('Keine Bilder konnten erfolgreich hochgeladen werden');
            }

            // Reload car with fresh images
            $car = $car->fresh(['images']);

            Log::info("Upload completed successfully", [
                'car_id' => $car->id,
                'uploaded_count' => count($uploadedImages),
                'total_images' => $car->images->count()
            ]);

            return response()->json([
                'message' => count($uploadedImages) . ' Bild(er) erfolgreich hochgeladen',
                'car' => new CarResource($car),
                'uploaded_images' => $uploadedImages,
                'success' => true
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error during image upload", [
                'errors' => $e->errors(),
                'kennzeichen' => $kennzeichen,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Validierungsfehler',
                'details' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Car not found for image upload", [
                'kennzeichen' => $kennzeichen,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Fahrzeug nicht gefunden'
            ], 404);
        } catch (Exception $e) {
            Log::error('Fehler beim Hochladen der Bilder: ' . $e->getMessage(), [
                'kennzeichen' => $kennzeichen,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['images']) // Exclude files from log
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Fehler beim Hochladen der Bilder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get human-readable upload error message
     */
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_OK:
                return 'Kein Fehler';
            case UPLOAD_ERR_INI_SIZE:
                return 'Datei größer als upload_max_filesize';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Datei größer als HTML-Formular-Limit';
            case UPLOAD_ERR_PARTIAL:
                return 'Datei nur teilweise hochgeladen';
            case UPLOAD_ERR_NO_FILE:
                return 'Keine Datei hochgeladen';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Temporäres Verzeichnis fehlt';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Schreibfehler auf Festplatte';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload durch PHP-Erweiterung gestoppt';
            default:
                return 'Unbekannter Upload-Fehler: ' . $errorCode;
        }
    }

    /**
     * Delete specific image
     */
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
                    'success' => false,
                    'error' => 'Bild-ID nicht gefunden',
                    'message' => 'Ungültige Bild-ID'
                ], 400);
            }

            $image = Image::findOrFail($imageId);

            Log::info("Found image to delete", [
                'image_id' => $image->id,
                'image_path' => $image->path
            ]);

            // Delete file from storage
            if ($image->path) {
                $imagePath = str_replace('storage/', '', $image->path);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                    Log::info("File deleted from storage", ['path' => $imagePath]);
                } else {
                    Log::warning("File not found in storage", ['path' => $imagePath]);
                }
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
            return response()->json([
                'success' => false,
                'error' => 'Bild nicht gefunden'
            ], 404);
        } catch (Exception $e) {
            Log::error('Fehler beim Löschen des Bildes: ' . $e->getMessage(), [
                'imageId' => $imageId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Fehler beim Löschen des Bildes: ' . $e->getMessage()
            ], 500);
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
                    'success' => false,
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
                    'success' => false,
                    'error' => 'Bild nicht gefunden',
                    'message' => 'Das angegebene Bild existiert nicht'
                ], 404);
            }

            $validatedData = $request->validate([
                'image' => 'required|file|image|max:16384|mimes:jpeg,png,jpg,gif,svg',
            ]);

            Log::info("Found image to replace", [
                'image_id' => $image->id,
                'old_path' => $image->path
            ]);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $newImage = $request->file('image');
                
                // Delete old file
                if ($image->path) {
                    $oldImagePath = str_replace('storage/', '', $image->path);
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                        Log::info("Old file deleted", ['path' => $oldImagePath]);
                    }
                }

                // Store new file with unique name
                $extension = $newImage->getClientOriginalExtension();
                $filename = uniqid('car_' . $car->id . '_') . '.' . $extension;
                $newPath = $newImage->storeAs('cars', $filename, 'public');
                
                if (!$newPath) {
                    throw new Exception('Neue Datei konnte nicht gespeichert werden');
                }
                
                $image->update(['path' => $newPath]);
                
                Log::info("New file stored", ['new_path' => $newPath]);
            } else {
                throw new Exception('Keine gültige Datei hochgeladen');
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
            return response()->json([
                'success' => false,
                'error' => 'Validierungsfehler',
                'details' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Car not found for replacement", [
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Fahrzeug nicht gefunden'
            ], 404);
        } catch (Exception $e) {
            Log::error('Fehler beim Ersetzen des Bildes: ' . $e->getMessage(), [
                'kennzeichen' => $kennzeichen,
                'imageId' => $imageId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Fehler beim Ersetzen des Bildes: ' . $e->getMessage()
            ], 500);
        }
    }
}