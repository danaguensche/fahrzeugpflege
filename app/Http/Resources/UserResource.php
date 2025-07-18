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
            'id'          => $this->id,
            'firstname'   => $this->firstname ?? 'Nicht verfügbar',
            'lastname'    => $this->lastname ?? 'Nicht verfügbar',
            'phonenumber' => $this->phonenumber ?? 'Nicht verfügbar',
            'email'       => $this->email ?? 'Nicht verfügbar',
            'addressline' => $this->addressline ?? 'Nicht verfügbar',
            'postalcode'  => $this->postalcode ?? 'Nicht verfügbar',
            'city'        => $this->city ?? 'Nicht verfügbar',
            'role'        => $this->role ?? 'Nicht verfügbar'
        ];
    }
    
}
