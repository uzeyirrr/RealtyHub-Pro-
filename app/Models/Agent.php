<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

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
        'working_areas'
    ];

    protected $casts = [
        'specializations' => 'array',
        'languages' => 'array',
        'social_media' => 'array',
        'working_areas' => 'array',
        'is_manager' => 'boolean',
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'license_date' => 'date'
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
        return $this->hasMany(Property::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
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

    // Yardımcı metodlar
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo ? asset("storage/{$this->photo}") : asset('images/default-agent-photo.png');
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
}
