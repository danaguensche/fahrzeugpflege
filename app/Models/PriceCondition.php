<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceCondition extends Model
{
    /** @use HasFactory<\Database\Factories\PriceConditionFactory> */
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id',
        'condition',
    ];

    public function servicePricings()
    {
        return $this->hasMany(ServicePricing::class);
    }
    public function extraCharges()
    {
        return $this->hasMany(ExtraCharge::class);
    }
}
