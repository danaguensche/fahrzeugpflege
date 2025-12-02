<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Service;
use App\Models\JobService;

class JobServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //     // Get all existing services
    //     $services = Service::all();
        
    //     // Get all existing jobs
    //     $jobs = Job::all();
        
    //     // If no jobs exist, create some
    //     if ($jobs->isEmpty()) {
    //         $jobs = Job::factory()->count(10)->create();
    //     }
        
    //     // If no services exist, shouldn't happen if ServiceSeeder runs first
    //     if ($services->isEmpty()) {
    //         $this->command->warn('No services found. Please run ServiceSeeder first.');
    //         return;
    //     }
        
    //     // Create job-service relationships
    //     foreach ($jobs as $job) {
    //         // Attach 1-5 random services to each job (adjust range as needed)
    //         $numberOfServices = rand(1, min(5, $services->count()));
    //         $randomServices = $services->random($numberOfServices);
            
    //         foreach ($randomServices as $service) {
    //             JobService::create([
    //                 'job_id' => $job->id,
    //                 'service_id' => $service->id,
    //             ]);
    //         }
    //     }
    }
}