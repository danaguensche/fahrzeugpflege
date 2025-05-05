<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

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
}
