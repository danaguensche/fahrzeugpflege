<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'Kennzeichen',
        'Fahrzeugklasse',
        'Automarke',
        'Typ',
        'Farbe',
        'Sonstiges',
        'image'
    ];
}
