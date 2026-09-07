<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobService extends Model
{

    protected $table = 'job_service';
    
    protected $fillable = [
        'job_id',
        'service_id',
    ];
}
