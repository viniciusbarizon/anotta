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
        ->toBe(mb_trim($normalizedName));
});

it('removes spaces in the middle of the name', function () {
    $normalizedName = normalize_name(fake()->firstName().'  '.fake()->lastName());

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
