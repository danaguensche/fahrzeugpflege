<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            'Kennzeichen' => $this->Kennzeichen,
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'Fahrzeugklasse' => $this->Fahrzeugklasse,
            'Automarke' => $this->Automarke,
            'Typ' => $this->Typ,
            'Farbe' => $this->Farbe,
            'Sonstiges' => $this->Sonstiges,
            'images' => $this->images->map(fn($img) => asset('storage/' . $img->path)),
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'firstname' => $this->customer->firstname,
                    'lastname' => $this->customer->lastname,
                    'email' => $this->customer->email,
                ];
            }),
        ];
    }
}
