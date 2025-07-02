<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\UserObserver;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;

#[ObservedBy([UserObserver::class])]
final class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Impersonate;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'is_super_admin',
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
     * Determine if the user can be impersonated.
     *
     * A user can be impersonated if they are not a super admin.
     *
     * @return bool True if the user can be impersonated, false otherwise.
     */
    public function canBeImpersonated(): bool
    {
        return $this->is_super_admin === false;
    }

    /**
     * Determine if the user has permission to impersonate other users.
     *
     * @return bool True if the user is a super admin and can impersonate others, false otherwise.
     */
    public function canImpersonate(): bool
    {
        return $this->is_super_admin;
    }

    /**
     * Get the clinics associated with the user.
     *
     * @return BelongsToMany A relationship to clinics
     */
    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class);
    }

    /**
     * Get the clinics associated with the user, including soft-deleted clinics.
     *
     * @return BelongsToMany A relationship to clinics, including soft-deleted ones
     */
    public function clinicsWithTrashed(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class)
            ->withTrashed();
    }

    /**
     * Determine if the user can access a specific panel.
     *
     * @param  Panel  $panel  The panel being accessed
     * @return bool Whether the user is allowed to access the panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get the attributes that should be cast to native types.
     *
     * @return array<string, string> The array of attribute casts.
     *
     * - 'email_verified_at': Casts to a datetime instance.
     * - 'password': Casts to a hashed value.
     * - 'is_super_admin': Casts to a boolean value.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    #[Scope]
    /**
     * Scope a query to only include users who have not verified their email.
     */
    protected function emailUnverified(Builder $builder): void
    {
        $builder->whereNull('email_verified_at');
    }

    #[Scope]
    /**
     * Scope a query to only include users who have verified their email.
     */
    protected function emailVerified(Builder $builder): void
    {
        $builder->whereNotNull('email_verified_at');
    }
}
