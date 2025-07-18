<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Http\Resources\JobResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JobDetailsController extends Controller
{
    public function details($id)
    {
        Log::info('JobDetailsController@details called', ['id' => $id, 'user_id' => auth()->check() ? auth()->id() : null, 'authenticated' => auth()->check()]);
        $job = Job::with(['customer', 'car', 'services'])->find($id);

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

                return response()->json([
                    'success' => true,
                    'message' => 'Jobdaten erfolgreich aktualisiert',
                    'data' => new JobResource($job->fresh(['customer', 'car', 'services']))
                ]);

            } else { // Admin or Trainer
                $validationRules = [
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'scheduled_at' => 'nullable|date',
                    'status' => 'required|string|max:255',
                    'customer_id' => 'nullable|exists:customers,id',
                    'car_id' => 'nullable|exists:cars,id',
                    'services' => 'nullable|array',
                    'services.*' => 'exists:services,id',
                ];

                $validatedData = $request->validate($validationRules);

                $job->update($validatedData);

                if (isset($validatedData['services'])) {
                    $job->services()->sync($validatedData['services']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Jobdaten erfolgreich aktualisiert',
                    'data' => new JobResource($job->fresh(['customer', 'car', 'services']))
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
}
