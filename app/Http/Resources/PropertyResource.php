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
            'title' => $this->title,
            'type' => $this->type,
            'status' => $this->status,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'description' => $this->description,
            'location' => [
                'address' => $this->address,
                'city' => $this->city,
                'district' => $this->district,
                'neighborhood' => $this->neighborhood,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'details' => $this->details,
            'media' => [
                'main_image' => $this->media['main_image'] ?? asset('images/property-placeholder.jpg'),
                'images' => $this->media['images'] ?? [],
                'video_url' => $this->video_url,
                'virtual_tour_url' => $this->virtual_tour_url,
            ],
            'agent' => new AgentResource($this->whenLoaded('agent')),
            'office' => new RealEstateOfficeResource($this->whenLoaded('office')),
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
