<?php

it('converts to lower case', function () {
    $name = strtoupper(fake()->name());

    expect(normalize_name($name))
        ->toBe(strtolower($name));
});

it('trims', function () {
    $name = normalize_name(fake()->name());

    expect(normalize_name($name . ' '))
        ->toBe($name);
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
