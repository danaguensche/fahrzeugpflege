<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarGroupSubgroup extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'id_car_group'
    ];

    public function carGroup()
    {
        return $this->belongsTo(CarGroup::class, 'Fahrzeugklasse');
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
