<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'Fahrzeugklasse' => $this->Fahrzeugklasse,
            'Automarke' => $this->Automarke,
            'Typ' => $this->Typ,
            'Farbe' => $this->Farbe,
            'Sonstiges' => $this->Sonstiges
        ];
    }
}
