<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Toplu atanabilir özellikler
     */
    protected $fillable = [
        'user_id',
        'office_id',
        'title',
        'phone',
        'photo',
        'license_number',
        'license_date',
        'specializations',
        'languages',
        'about',
        'social_media',
        'is_manager',
        'is_active',
        'commission_rate',
        'working_areas',
        'stats'
    ];

    /**
     * Tip dönüşümleri
     */
    protected $casts = [
        'specializations' => 'array',
        'languages' => 'array',
        'social_media' => 'array',
        'working_areas' => 'array',
        'stats' => 'array',
        'is_manager' => 'boolean',
        'is_active' => 'boolean',
        'commission_rate' => 'float',
        'license_date' => 'date'
    ];

    /**
     * Varsayılan değerler
     */
    protected $attributes = [
        'is_active' => true,
        'is_manager' => false,
        'stats' => '{"active_listings_count": 0, "active_customers_count": 0, "total_sales": 0, "total_rentals": 0}'
    ];

    // İlişkiler
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(RealEstateOffice::class, 'office_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'agent_id');
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeManagers($query)
    {
        return $query->where('is_manager', true);
    }

    public function scopeWithSpecialization($query, $specialization)
    {
        return $query->where('specializations', 'like', "%{$specialization}%");
    }

    public function scopeWithLanguage($query, $language)
    {
        return $query->where('languages', 'like', "%{$language}%");
    }

    public function scopeInWorkingArea($query, $area)
    {
        return $query->where('working_areas', 'like', "%{$area}%");
    }

    // Yardımcı metodlar
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo) {
            return asset('images/default-agent.png');
        }

        return Storage::disk('public')->url($this->photo);
    }

    public function getActiveListingsCountAttribute(): int
    {
        return $this->properties()->active()->count();
    }

    public function getTotalSalesAttribute(): int
    {
        return $this->properties()->where('status_text', 'sold')->count();
    }

    public function getTotalRentalsAttribute(): int
    {
        return $this->properties()->where('status_text', 'rented')->count();
    }

    public function getActiveCustomersCountAttribute(): int
    {
        return $this->customers()->active()->count();
    }

    public function updateStats()
    {
        $stats = [
            'active_listings_count' => $this->properties()->active()->count(),
            'active_customers_count' => $this->customers()->active()->count(),
            'total_sales' => $this->properties()->sold()->count(),
            'total_rentals' => $this->properties()->rented()->count()
        ];

        $this->update(['stats' => $stats]);
    }
}
