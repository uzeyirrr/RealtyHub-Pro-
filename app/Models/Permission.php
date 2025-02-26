<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
    ];

    /**
     * İzin ile ilişkili roller
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * İzin ile ilişkili kullanıcılar
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * İzni verilen role ekler
     */
    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching($role);
    }

    /**
     * İzni verilen rolden kaldırır
     */
    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->roles()->detach($role);
    }

    /**
     * İzin ile ilişkili tüm rolleri senkronize eder
     */
    public function syncRoles(array $roles): void
    {
        $this->roles()->sync($roles);
    }

    /**
     * İzin ile ilişkili tüm kullanıcıları senkronize eder
     */
    public function syncUsers(array $users): void
    {
        $this->users()->sync($users);
    }
} 