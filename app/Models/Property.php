<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'status',
        'price',
        'description',
        'location',
        'details',
        'media',
        'stats',
        'agent_id',
        'office_id'
    ];

    protected $casts = [
        'location' => 'array',
        'details' => 'array',
        'media' => 'array',
        'stats' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'formatted_price',
    ];

    /**
     * İlanın danışmanı
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * İlanın bağlı olduğu ofis
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(RealEstateOffice::class);
    }

    /**
     * Aktif ilanları filtreler
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Öne çıkan ilanları filtreler
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Fiyatı formatlar
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ₺';
    }

    /**
     * İstatistikleri günceller
     */
    public function incrementStats(string $key): void
    {
        $stats = $this->stats ?? [
            'view_count' => 0,
            'favorite_count' => 0,
        ];

        $stats[$key] = ($stats[$key] ?? 0) + 1;
        $this->stats = $stats;
        $this->save();
    }

    /**
     * İlanı görüntülenme sayısını artırır
     */
    public function incrementViewCount(): void
    {
        $this->incrementStats('view_count');
    }

    /**
     * İlanın favori eklenme sayısını artırır
     */
    public function incrementFavoriteCount(): void
    {
        $this->incrementStats('favorite_count');
    }

    /**
     * İlanın favori eklenme sayısını azaltır
     */
    public function decrementFavoriteCount(): void
    {
        $stats = $this->stats;
        $stats['favorite_count'] = max(0, ($stats['favorite_count'] ?? 0) - 1);
        $this->stats = $stats;
        $this->save();
    }
}
