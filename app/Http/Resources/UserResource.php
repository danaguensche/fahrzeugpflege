<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'firstName'   => $this->firstname ?? 'Nicht verfügbar',
            'lastName'    => $this->lastname ?? 'Nicht verfügbar',
            'phoneNumber' => $this->phonenumber ?? 'Nicht verfügbar',
            'email'       => $this->email ?? 'Nicht verfügbar',
            'addressLine' => $this->addressline ?? 'Nicht verfügbar',
            'postalCode'  => $this->postalcode ?? 'Nicht verfügbar',
            'city'        => $this->city ?? 'Nicht verfügbar'
        ];
    }
    
}
