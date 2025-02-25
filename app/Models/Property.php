<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'office_id',
        'agent_id',
        'title',
        'slug',
        'description',
        'type',
        'status',
        'price',
        'currency',
        'rooms',
        'bathrooms',
        'floor',
        'total_floors',
        'age',
        'gross_sqm',
        'net_sqm',
        'heating_type',
        'is_furnished',
        'address',
        'city',
        'district',
        'neighborhood',
        'latitude',
        'longitude',
        'features',
        'media',
        'video_url',
        'virtual_tour_url',
        'is_active',
        'is_featured',
        'featured_until',
        'status_text',
        'commission_rate',
        'seo'
    ];

    protected $casts = [
        'features' => 'array',
        'media' => 'array',
        'seo' => 'array',
        'is_furnished' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
        'price' => 'decimal:2',
        'gross_sqm' => 'decimal:2',
        'net_sqm' => 'decimal:2',
        'commission_rate' => 'decimal:2'
    ];

    // İlişkiler
    public function office(): BelongsTo
    {
        return $this->belongsTo(RealEstateOffice::class, 'office_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    public function scopeForSale($query)
    {
        return $query->where('status', 'sale');
    }

    public function scopeForRent($query)
    {
        return $query->where('status', 'rent');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status_text', 'available');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeInDistrict($query, $district)
    {
        return $query->where('district', $district);
    }

    // Yardımcı metodlar
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->neighborhood}, {$this->district}/{$this->city}";
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getMainImageAttribute(): string
    {
        $media = json_decode($this->media, true);
        return $media && isset($media[0]) ? asset("storage/{$media[0]}") : asset('images/default-property.png');
    }

    public function getPropertyUrlAttribute(): string
    {
        return route('properties.show', $this->slug);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function incrementFavoriteCount(): void
    {
        $this->increment('favorite_count');
    }

    public function decrementFavoriteCount(): void
    {
        $this->decrement('favorite_count');
    }
}
