<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Ayar değerini formatlar
     */
    public function getFormattedValueAttribute()
    {
        if ($this->type === 'boolean') {
            return (bool) $this->value;
        }

        if ($this->type === 'integer') {
            return (int) $this->value;
        }

        if ($this->type === 'array' || $this->type === 'json') {
            return json_decode($this->value, true);
        }

        return $this->value;
    }

    /**
     * Ayar değerini ayarlar
     */
    public function setValueAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['value'] = json_encode($value);
            return;
        }

        $this->attributes['value'] = $value;
    }

    /**
     * Belirli bir gruptaki ayarları getirir
     */
    public function scopeInGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Ayar değerini cache'leyerek getirir
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("settings.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->formatted_value : $default;
        });
    }

    /**
     * Ayar değerini günceller
     */
    public static function set($key, $value, $group = null, $type = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
            ]
        );

        Cache::forget("settings.{$key}");

        return $setting;
    }
} 