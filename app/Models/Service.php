<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'description',
    ];

    public function servicePricings()
    {
        return $this->hasMany(ServicePricing::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_service', 'service_id', 'job_id');
    }
}
