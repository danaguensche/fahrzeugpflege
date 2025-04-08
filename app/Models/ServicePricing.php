<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePricing extends Model
{
    /** @use HasFactory<\Database\Factories\ServicePricingFactory> */
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id',
        'id_service',
        'id_car_group',
        'price',
        'id_price_condition'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'id');
    }
    public function priceCondition()
    {
        return $this->belongsTo(PriceCondition::class, 'id');
    }
    public function carGroup()
    {
        return $this->belongsTo(CarGroup::class, 'id');
    }
}
