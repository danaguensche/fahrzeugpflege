<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Wenn die Resource null ist, geben wir einen leeren Array zurück
        if (is_null($this->resource)) {
            return [];
        }
        
        return [
            'id' => $this->id,
            'company' => $this->company,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phonenumber' => $this->phonenumber,
            'notes' => $this->notes,
            'addressline' => $this->addressline,
            'postalcode' => $this->postalcode,
            'city' => $this->city,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cars' => CarResource::collection($this->whenLoaded('cars')),
            'auftraege' => JobResource::collection($this->whenLoaded('auftraege')),
        ];
    }
}
