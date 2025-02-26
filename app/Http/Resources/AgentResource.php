<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
{
    /**
     * Kaynağı bir array'e dönüştür.
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
            'office' => [
                'id' => $this->office->id,
                'name' => $this->office->name,
            ],
            'title' => $this->title,
            'phone' => $this->phone,
            'photo_url' => $this->photo_url,
            'license_number' => $this->license_number,
            'license_date' => $this->license_date?->format('Y-m-d'),
            'specializations' => $this->specializations ?? [],
            'languages' => $this->languages ?? [],
            'about' => $this->about,
            'social_media' => $this->social_media ?? [],
            'is_manager' => $this->is_manager,
            'is_active' => $this->is_active,
            'commission_rate' => $this->commission_rate,
            'working_areas' => $this->working_areas ?? [],
            'stats' => $this->stats ?? [
                'active_listings_count' => 0,
                'active_customers_count' => 0,
                'total_sales' => 0,
                'total_rentals' => 0
            ],
            'properties' => $this->when($this->relationLoaded('properties'), function () {
                return PropertyResource::collection($this->properties);
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
