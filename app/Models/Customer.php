<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Customer extends Model
{
    use HasFactory, Searchable;
    
    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'company',
        'firstname',
        'lastname',
        'email',
        'phonenumber',
        'addressline',
        'postalcode',
        'city'
    ];


    public function cars()
    {
        return $this->hasMany(Car::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class); 
    }

    public function auftraege()
    {
        return $this->hasMany(Job::class);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phonenumber' => $this->phonenumber,
            'addressline' => $this->addressline,
            'postalcode' => $this->postalcode,
            'city' => $this->city
        ];
    }
}
