<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageReport extends Model
{
    protected $table = 'image_reports';

    protected $fillable = [
        'task_id',
        'user_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
    ];
}
