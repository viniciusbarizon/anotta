<?php

declare(strict_types=1);

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class Base extends Model
{
    use CascadeSoftDeletes;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;
}
