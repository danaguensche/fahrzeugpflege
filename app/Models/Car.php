<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'Kennzeichen',
        'Fahrzeugklasse',
        'Automarke',
        'Typ',
        'Farbe',
        'Sonstiges',
        'image'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function carGroup()
    {
        return $this->belongsTo(CarGroup::class, 'Fahrzeugklasse');
    }

    public function getRouteKeyName()
    {
        return 'Kennzeichen';
    }
}
 