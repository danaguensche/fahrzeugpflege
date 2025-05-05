<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderExtraCharge extends Model
{
    /** @use HasFactory<\Database\Factories\OrderExtraChargeFactory> */
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id',
        'id_order',
        'id_extra_charge'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'id');
    }
    public function extraCharge()
    {
        return $this->belongsTo(ExtraCharge::class, 'id');
    }
}
