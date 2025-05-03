<?php

use App\Actions\Clinic\HasAnotherWithSameNameAction;
use App\Models\Clinic;
use function Pest\Laravel\{actingAs};

it('returns false if it is the same name but the owner edition', function () {
    $clinic = Clinic::first();

    $has = hasAnotherWithSameName(
        currentId: $clinic->id,
        name: $clinic->name
    );

    expect($has)->toBeFalse();
});

function hasAnotherWithSameName(?string $currentId, string $name): bool
{
    return (new HasAnotherWithSameNameAction)->handle(
        currentId: $currentId,
        name: $name
    );
}
