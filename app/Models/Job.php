<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = 'auftraege';

    protected $fillable = [
        'title',
        'description',
        'car_id',
        'customer_id',
        'status',
        'scheduled_at',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'job_service', 'job_id', 'service_id');
    }

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
}