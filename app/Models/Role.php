<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Rol ile ilişkili izinler
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Rol ile ilişkili kullanıcılar
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Verilen izni role ekler
     */
    public function givePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching($permission);
    }

    /**
     * Verilen izni rolden kaldırır
     */
    public function revokePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission);
    }

    /**
     * Rolün verilen izne sahip olup olmadığını kontrol eder
     */
    public function hasPermissionTo(Permission|string $permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions()->where('slug', $permission)->exists();
        }

        return $this->permissions()->where('id', $permission->id)->exists();
    }

    /**
     * Rolün tüm izinlerini senkronize eder
     */
    public function syncPermissions(array $permissions): void
    {
        $this->permissions()->sync($permissions);
    }
} 