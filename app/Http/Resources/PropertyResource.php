<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'office' => new RealEstateOfficeResource($this->office),
            'agent' => new AgentResource($this->agent),
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'currency' => $this->currency,
            'details' => [
                'rooms' => $this->rooms,
                'bathrooms' => $this->bathrooms,
                'floor' => $this->floor,
                'total_floors' => $this->total_floors,
                'age' => $this->age,
                'gross_sqm' => $this->gross_sqm,
                'net_sqm' => $this->net_sqm,
                'heating_type' => $this->heating_type,
                'is_furnished' => $this->is_furnished,
            ],
            'location' => [
                'address' => $this->address,
                'full_address' => $this->full_address,
                'city' => $this->city,
                'district' => $this->district,
                'neighborhood' => $this->neighborhood,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'features' => $this->features,
            'media' => [
                'images' => $this->media,
                'main_image' => $this->main_image,
                'video_url' => $this->video_url,
                'virtual_tour_url' => $this->virtual_tour_url,
            ],
            'status_info' => [
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
                'featured_until' => $this->featured_until,
                'status_text' => $this->status_text,
            ],
            'stats' => [
                'view_count' => $this->view_count,
                'favorite_count' => $this->favorite_count,
            ],
            'commission_rate' => $this->commission_rate,
            'seo' => $this->seo,
            'property_url' => $this->property_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
