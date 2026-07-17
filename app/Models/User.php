<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole(string $role): bool
    {
        if ($this->relationLoaded('roles') || $this->exists) {
            $this->loadMissing('roles');
            if ($this->roles->isNotEmpty()) {
                return $this->roles->contains('name', $role);
            }
        }

        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        if ($this->relationLoaded('roles') || $this->exists) {
            $this->loadMissing('roles');
            if ($this->roles->isNotEmpty()) {
                return $this->roles->whereIn('name', $roles)->isNotEmpty();
            }
        }

        return in_array($this->role, $roles);
    }

    public function isSuperadmin(): bool
    {
        if ($this->relationLoaded('roles') || $this->exists) {
            $this->loadMissing('roles');
            if ($this->roles->isNotEmpty()) {
                return $this->roles->contains('name', 'superadmin');
            }
        }

        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        if ($this->relationLoaded('roles') || $this->exists) {
            $this->loadMissing('roles');
            if ($this->roles->isNotEmpty()) {
                return $this->hasAnyRole(['superadmin', 'admin']);
            }
        }

        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
    }
}
