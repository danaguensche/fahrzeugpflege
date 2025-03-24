<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarGroup extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
    ];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
