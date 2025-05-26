<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class UserRelationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * This scope restricts the query results based on the authenticated user's role.
     * If the authenticated user is a super admin, no restrictions are applied.
     * Otherwise, the query is filtered to include only records related to the authenticated user.
     *
     * @param  Builder  $builder  The query builder instance.
     * @param  Model  $model  The Eloquent model instance.
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->is_super_admin) {
            return;
        }

        $builder->whereRelation('users', 'user_id', $user->id);
    }
}
