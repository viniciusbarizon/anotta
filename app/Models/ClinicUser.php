<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ClinicUser extends Pivot
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;
}
