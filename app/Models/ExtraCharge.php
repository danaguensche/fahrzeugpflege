<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraCharge extends Model
{
    public $timestamps = false;
    /** @use HasFactory<\Database\Factories\ExtraChargeFactory> */
    use HasFactory;
    protected $fillable = [
        'id',
        'id_order',
        'price',
        'id_price_condition'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id');
    }
    public function priceCondition()
    {
        return $this->belongsTo(PriceCondition::class, 'id');
    }
}
