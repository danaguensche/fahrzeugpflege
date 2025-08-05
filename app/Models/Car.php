<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Car extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'id',
        'Kennzeichen',
        'Fahrzeugklasse',
        'Automarke',
        'Typ',
        'Farbe',
        'Sonstiges',
        'image', 
        'customer_id'
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
        
    public function carGroupSubgroup()
    {
        return $this->belongsTo(CarGroupSubgroup::class, 'Fahrzeugklasse');
    }
    public function orders()
    {
        return $this->hasMany(Order::class); 
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'Kennzeichen' => $this->Kennzeichen,
            'Fahrzeugklasse' => $this->Fahrzeugklasse,
            'Automarke' => $this->Automarke,
            'Typ' => $this->Typ,
            'Farbe' => $this->Farbe,
            'Sonstiges' => $this->Sonstiges,
            'customer_id' => $this->customer_id,
        ];
    }
}
 