<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ClinicUserRelationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * This method filters the query to include only those clinics that are associated with the currently authenticated user.
     *
     * @param  Builder  $builder  The Eloquent query builder instance.
     * @param  Model  $model  The model being queried.
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var User $user */
        $user = auth()->user();

        $builder->whereRelation('clinic.users', 'user_id', $user->id);
    }
}
