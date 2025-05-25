<?php

declare(strict_types=1);

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class Base extends Model
{
    use CascadeSoftDeletes;
    use HasUlids;
    use SoftDeletes;

    #[Scope]
    /**
     * Adds a condition to the query to exclude a specific ID if provided.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  string|null  $id  The ID to exclude from the query. If null, no exclusion is applied.
     */
    protected function notCurrentId(Builder $query, ?string $id): void
    {
        $query->when(is_null($id) === false, function (Builder $query) use ($id) {
            $query->where('id', '!=', $id);
        });
    }
}
