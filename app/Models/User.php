<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
        'company_name',
        'phone',
        'settings',
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
            'password'          => 'hashed',
            'settings'          => 'array',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The companies that the user has access to.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    /**
     * A user has many posts.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Determine if the user has the admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Determine if the user has the client role.
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Determine if the user has permission to publish posts.
     */
    public function canPublish(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return (bool) ($this->settings['can_publish'] ?? false);
    }

    /**
     * Check if email notifications are enabled for the user.
     * Defaults to false so emails are opt-in by the client.
     */
    public function emailNotificationsEnabled(): bool
    {
        return (bool) data_get($this->settings, 'notifications.email', false);
    }

    /**
     * Cache companies count per request to prevent redundant count queries in views.
     */
    protected ?int $memoizedCompaniesCount = null;

    /**
     * Check if the user belongs to multiple companies (memoized).
     */
    public function hasMultipleCompanies(): bool
    {
        if ($this->memoizedCompaniesCount === null) {
            if ($this->relationLoaded('companies')) {
                $this->memoizedCompaniesCount = $this->companies->count();
            } else {
                $this->memoizedCompaniesCount = $this->companies()->count();
            }
        }

        return $this->memoizedCompaniesCount > 1;
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtolower($value),
            get: fn(string $value) => strtolower($value),
        );
    }
}
