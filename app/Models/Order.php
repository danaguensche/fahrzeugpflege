<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = [
        'id',
        'id_customer',
        'id_car',
        'id_service_pricing',
        'created_at'
    ];
}
