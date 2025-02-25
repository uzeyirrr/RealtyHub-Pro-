<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealEstateOffice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'phone',
        'email',
        'tax_number',
        'address',
        'latitude',
        'longitude',
        'city',
        'district',
        'website',
        'is_franchise',
        'parent_office_id',
        'working_hours',
        'social_media',
        'about',
        'is_active'
    ];

    protected $casts = [
        'working_hours' => 'array',
        'social_media' => 'array',
        'is_franchise' => 'boolean',
        'is_active' => 'boolean',
    ];

    // İlişkiler
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'office_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'office_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'office_id');
    }

    public function parentOffice(): BelongsTo
    {
        return $this->belongsTo(RealEstateOffice::class, 'parent_office_id');
    }

    public function franchises(): HasMany
    {
        return $this->hasMany(RealEstateOffice::class, 'parent_office_id');
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFranchises($query)
    {
        return $query->where('is_franchise', true);
    }

    public function scopeMainOffices($query)
    {
        return $query->whereNull('parent_office_id');
    }

    // Yardımcı metodlar
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->district}/{$this->city}";
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset("storage/{$this->logo}") : asset('images/default-office-logo.png');
    }
}
