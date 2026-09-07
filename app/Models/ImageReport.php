<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageReport extends Model
{
    protected $table = 'image_reports';

    protected $fillable = [
        'job_id',
        'user_id',
        'path',
        'file_name',
        'file_size',
        'mime_type',
    ];

    protected $attributes = [
        'file_name' => '',
        'file_size' => 0,
        'mime_type' => '',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
