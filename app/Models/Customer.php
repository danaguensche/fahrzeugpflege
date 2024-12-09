<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable =[
        'company',
        'firstname',
        'lastname',
        'email',
        'phonenumber',
        'adressline',
        'postalcode',
        'city'
    ];


    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
