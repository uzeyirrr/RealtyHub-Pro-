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
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'agent' => $this->when($this->agent_id, new AgentResource($this->agent)),
            'office' => $this->when($this->office_id, new RealEstateOfficeResource($this->office)),
            'contact' => [
                'phone' => $this->phone,
                'alternate_phone' => $this->alternate_phone,
                'address' => $this->address,
                'full_address' => $this->full_address,
                'city' => $this->city,
                'district' => $this->district,
            ],
            'business' => [
                'customer_type' => $this->customer_type,
                'tax_number' => $this->tax_number,
                'tax_office' => $this->tax_office,
                'company_name' => $this->company_name,
            ],
            'preferences' => $this->preferences,
            'activity' => [
                'search_history' => $this->search_history,
                'viewed_properties' => $this->viewed_properties,
                'favorite_properties' => $this->favorite_properties,
            ],
            'lead_info' => [
                'lead_source' => $this->lead_source,
                'lead_status' => $this->lead_status,
                'notes' => $this->notes,
                'last_contact' => $this->last_contact?->format('Y-m-d H:i:s'),
                'next_followup' => $this->next_followup?->format('Y-m-d H:i:s'),
            ],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
