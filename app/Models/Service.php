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
        'id',
        'title',
    ];

    public function servicePricings()
    {
        return $this->hasMany(ServicePricing::class);
    }
}
