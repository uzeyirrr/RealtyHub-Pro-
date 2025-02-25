<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'office' => new RealEstateOfficeResource($this->office),
            'title' => $this->title,
            'phone' => $this->phone,
            'photo_url' => $this->photo_url,
            'license_number' => $this->license_number,
            'license_date' => $this->license_date?->format('Y-m-d'),
            'specializations' => $this->specializations,
            'languages' => $this->languages,
            'about' => $this->about,
            'social_media' => $this->social_media,
            'is_manager' => $this->is_manager,
            'is_active' => $this->is_active,
            'commission_rate' => $this->commission_rate,
            'working_areas' => $this->working_areas,
            'stats' => [
                'active_listings_count' => $this->active_listings_count,
                'total_sales' => $this->total_sales,
                'total_rentals' => $this->total_rentals,
                'active_customers_count' => $this->active_customers_count,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
