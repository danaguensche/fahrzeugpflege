<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'Title' => $this->title,
            'Beschreibung' => $this->description,
            'Abholtermin' => $this->scheduled_at,
            'Status' => $this->status,
            'trainee' => new UserResource($this->whenLoaded('trainee')),
            'customer_id' => $this->customer_id,
            'car_id' => $this->car_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'car' => new CarResource($this->whenLoaded('car')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function trainee()
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }
}
