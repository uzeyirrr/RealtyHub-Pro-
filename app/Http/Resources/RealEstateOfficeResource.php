<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RealEstateOfficeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_url' => $this->logo_url,
            'phone' => $this->phone,
            'email' => $this->email,
            'tax_number' => $this->tax_number,
            'address' => $this->address,
            'full_address' => $this->full_address,
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'city' => $this->city,
            'district' => $this->district,
            'website' => $this->website,
            'is_franchise' => $this->is_franchise,
            'parent_office' => $this->when($this->parent_office_id, new RealEstateOfficeResource($this->parentOffice)),
            'working_hours' => $this->working_hours,
            'social_media' => $this->social_media,
            'about' => $this->about,
            'is_active' => $this->is_active,
            'stats' => [
                'agents_count' => $this->agents()->count(),
                'active_properties_count' => $this->properties()->active()->count(),
                'total_properties_count' => $this->properties()->count(),
                'customers_count' => $this->customers()->count(),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
