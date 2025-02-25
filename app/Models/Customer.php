<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'agent_id',
        'office_id',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'district',
        'customer_type',
        'tax_number',
        'tax_office',
        'company_name',
        'preferences',
        'search_history',
        'viewed_properties',
        'favorite_properties',
        'lead_source',
        'lead_status',
        'notes',
        'last_contact',
        'next_followup',
        'is_active'
    ];

    protected $casts = [
        'preferences' => 'array',
        'search_history' => 'array',
        'viewed_properties' => 'array',
        'favorite_properties' => 'array',
        'is_active' => 'boolean',
        'last_contact' => 'datetime',
        'next_followup' => 'datetime'
    ];

    // İlişkiler
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(RealEstateOffice::class, 'office_id');
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('customer_type', $type);
    }

    public function scopeByLeadStatus($query, $status)
    {
        return $query->where('lead_status', $status);
    }

    public function scopeNeedsFollowup($query)
    {
        return $query->where('next_followup', '<=', now());
    }

    // Yardımcı metodlar
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getFullAddressAttribute(): string
    {
        return $this->address ? "{$this->address}, {$this->district}/{$this->city}" : null;
    }

    public function addToViewedProperties($propertyId): void
    {
        $viewed = $this->viewed_properties ?? [];
        if (!in_array($propertyId, $viewed)) {
            $viewed[] = $propertyId;
            $this->update(['viewed_properties' => $viewed]);
        }
    }

    public function addToFavorites($propertyId): void
    {
        $favorites = $this->favorite_properties ?? [];
        if (!in_array($propertyId, $favorites)) {
            $favorites[] = $propertyId;
            $this->update(['favorite_properties' => $favorites]);
        }
    }

    public function removeFromFavorites($propertyId): void
    {
        $favorites = $this->favorite_properties ?? [];
        if (($key = array_search($propertyId, $favorites)) !== false) {
            unset($favorites[$key]);
            $this->update(['favorite_properties' => array_values($favorites)]);
        }
    }

    public function updateLastContact(): void
    {
        $this->update(['last_contact' => now()]);
    }

    public function scheduleNextFollowup($date): void
    {
        $this->update(['next_followup' => $date]);
    }
}
