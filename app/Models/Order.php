<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $table = 'auftraege';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'id_customer',
        'id_car',
        'id_service_pricing',
        'created_at',
        'trainer_id',
        'trainee_id',
        'title',
        'description',
        'scheduled_at',
        'status'
    ];

    public function servicePricing()
    {
        return $this->belongsTo(ServicePricing::class, 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id');
    }
    public function car()
    {
        return $this->belongsTo(Car::class, 'id');
    }
    public function orderExtraCharges()
    {
        return $this->hasMany(OrderExtraCharge::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'order_id');
    }
}
