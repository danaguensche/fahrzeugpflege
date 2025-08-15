<?php

namespace App\Http\Controllers;

use App\Models\ImageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImageReportController extends Controller
{
    public function index($taskId)
    {
        try {
            $images = ImageReport::where('job_id', $taskId)->get();
            return response()->json($images);
        } catch (\Exception $e) {
            Log::error('Error fetching task images: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch images'], 500);
        }
    }

    public function upload(Request $request, $taskId)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,webp|max:10240', // 10MB
            ]);

            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'tasks/' . $taskId;
            $file->storeAs('public/' . $filePath, $fileName);

            $imageReport = ImageReport::create([
                'job_id' => $taskId,
                'user_id' => auth()->user()->id(),
                'file_path' => $filePath . '/' . $fileName,
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            Log::info('Task image uploaded successfully', [
                'task_id' => $taskId,
                'image_id' => $imageReport->id,
                'file_name' => $fileName
            ]);

            return response()->json($imageReport, 201);
        } catch (\Exception $e) {
            Log::error('Error uploading task image: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload image'], 500);
        }
    }

    public function destroy($imageId)
    {
        try {
            $imageReport = ImageReport::findOrFail($imageId);

            // Check permissions
            if (Auth::user()->role === 'trainee' && $imageReport->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Delete file from storage
            $filePath = 'public/' . $imageReport->file_path;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                Log::info('Task image file deleted from storage', ['path' => $filePath]);
            } else {
                Log::warning('Task image file not found in storage', ['path' => $filePath]);
            }

            // Delete database record
            $imageReport->delete();

            Log::info('Task image deleted successfully', [
                'image_id' => $imageId,
                'user_id' => Auth::id()
            ]);

            return response()->json(['message' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting task image: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete image'], 500);
        }
    }
}