<?php

namespace App\Http\Controllers;

use App\Models\ImageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ImageReportController extends Controller
{
    public function index($taskId)
    {
        $images = ImageReport::where('job_id', $taskId)->get();

        return response()->json($images);
    }

    public function upload(Request $request, $taskId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,webp|max:10240', // 10MB
        ]);

        $file = $request->file('image');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = 'tasks/' . $taskId;

        $file->storeAs('public/' . $filePath, $fileName);

        $imageReport = ImageReport::create([
            'job_id' => $taskId,
            'user_id' => auth()->id,
            'file_path' => $filePath . '/' . $fileName,
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json($imageReport, 201);
    }

    public function destroy($imageId)
    {
        $imageReport = ImageReport::findOrFail($imageId);

        if (Auth::user()->role === 'trainee' && $imageReport->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::delete('public/' . $imageReport->file_path);
        $imageReport->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }
}
