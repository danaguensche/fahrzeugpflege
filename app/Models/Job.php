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
        'user_id',
        'status',
        'scheduled_at',
        'trainer_id',
        'trainee_id',
        'image',
    ];



    public function services()
    {
        return $this->belongsToMany(Service::class, 'job_service', 'job_id', 'service_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function trainee()
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }



    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function images()
    {
        return $this->hasMany(ImageReport::class, 'job_id');
    }
}
