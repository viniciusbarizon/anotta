<?php

declare(strict_types=1);

it('converts to lower case', function () {
    $name = mb_strtoupper(fake()->name());
    $normalizedName = normalize_name($name);

    expect($normalizedName)
        ->toBe(mb_strtolower($normalizedName));
});

it('trims', function () {
    $name = fake()->name().' ';
    $normalizedName = normalize_name($name);

    expect($normalizedName)
        ->toBe((string) str($normalizedName)->squish());
});

it('removes the accents', function () {
    $name = 'Vinícius';

    expect(normalize_name($name))
        ->toBe('vinicius');
});

it('removes non-word characters', function () {
    $name = 'Dr. Vinícius';

    expect(normalize_name($name))
        ->toBe('dr vinicius');
});
