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
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[ObservedBy([UserObserver::class])]
final class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are not mass assignable.
     *
     * This property prevents the specified attributes from being
     * mass-assigned to the model. In this case, the 'is_super_admin'
     * attribute is protected to ensure it cannot be modified
     * unintentionally through mass assignment.
     *
     * @var array
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
     * Get the clinician associated with the user.
     */
    public function clinician(): HasOne
    {
        return $this->hasOne(Clinician::class);
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
        return $this->hasVerifiedEmail();
    }

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

    #[Scope]
    /**
     * Scope a query to only include users who have not verified their email.
     */
    protected function emailUnverified(Builder $query): void
    {
        $query->whereNull('email_verified_at');
    }

    #[Scope]
    /**
     * Scope a query to only include users who have verified their email.
     */
    protected function emailVerified(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }

    #[Scope]
    /**
     * Scope a query to filter users that belong to a specific clinic.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  string  $clinicId  The ID of the clinic to filter by.
     */
    protected function belongsToClinic(Builder $query, string $clinicId): void
    {
        $query->whereRelation('clinics', 'id', $clinicId);
    }

    #[Scope]
    /**
     * Scope a query to filter users that belong to a specific clinic, including soft-deleted clinics.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  string  $clinicId  The ID of the clinic to filter by.
     */
    protected function belongsToClinicWithTrashed(Builder $query, string $clinicId): void
    {
        $query->whereRelation('clinicsWithTrashed', 'id', $clinicId);
    }
}
