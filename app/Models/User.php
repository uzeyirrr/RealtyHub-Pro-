<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'alternate_phone',
        'address',
        'bio',
        'photo',
        'status',
        'linkedin_url',
        'instagram_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Kullanıcı ile ilişkili roller
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Kullanıcı ile ilişkili izinler
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Kullanıcıya rol atar
     */
    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching($role);
    }

    /**
     * Kullanıcıdan rol kaldırır
     */
    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->roles()->detach($role);
    }

    /**
     * Kullanıcının rollerini senkronize eder
     */
    public function syncRoles(array $roles): void
    {
        $this->roles()->sync($roles);
    }

    /**
     * Kullanıcıya direkt izin verir
     */
    public function givePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching($permission);
    }

    /**
     * Kullanıcıdan direkt izin kaldırır
     */
    public function revokePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission);
    }

    /**
     * Kullanıcının izinlerini senkronize eder
     */
    public function syncPermissions(array $permissions): void
    {
        $this->permissions()->sync($permissions);
    }

    /**
     * Kullanıcının verilen role sahip olup olmadığını kontrol eder
     */
    public function hasRole(Role|string $role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }

        return $this->roles()->where('id', $role->id)->exists();
    }

    /**
     * Kullanıcının verilen izne sahip olup olmadığını kontrol eder
     */
    public function hasPermission(Permission|string $permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions()->where('slug', $permission)->exists() ||
                   $this->roles()->whereHas('permissions', function ($query) use ($permission) {
                       $query->where('slug', $permission);
                   })->exists();
        }

        return $this->permissions()->where('id', $permission->id)->exists() ||
               $this->roles()->whereHas('permissions', function ($query) use ($permission) {
                   $query->where('id', $permission->id);
               })->exists();
    }

    /**
     * Kullanıcının verilen izinlerden herhangi birine sahip olup olmadığını kontrol eder
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kullanıcının verilen izinlerin tümüne sahip olup olmadığını kontrol eder
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Kullanıcının aktif olup olmadığını kontrol eder
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Kullanıcının yönetici olup olmadığını kontrol eder
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Kullanıcının emlak danışmanı olup olmadığını kontrol eder
     */
    public function isAgent(): bool
    {
        return $this->hasRole('agent');
    }

    /**
     * Kullanıcının kıdemli emlak danışmanı olup olmadığını kontrol eder
     */
    public function isSeniorAgent(): bool
    {
        return $this->hasRole('senior_agent');
    }

    /**
     * Kullanıcının takım lideri olup olmadığını kontrol eder
     */
    public function isTeamLead(): bool
    {
        return $this->hasRole('team_lead');
    }
}
