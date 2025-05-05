<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserRelationScope implements Scope
{
    /**
     * Apply a global scope to the query builder to filter results based on the logged-in user.
     *
     * This scope adds a condition to the query to ensure that only records
     * related to the currently authenticated user are retrieved. It uses
     * the `whereRelation` method to filter based on the `users` relationship
     * and matches the `user_id` with the ID of the authenticated user.
     *
     * @param Builder $builder The query builder instance.
     * @param Model $model The model instance being queried.
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereRelation('users', 'user_id', auth()->user()->id);
    }
}
