<?php

declare(strict_types=1);

if (! function_exists('normalize_name')) {
    function normalize_name(string $name): string
    {
        return (string) str($name)
            ->lower()
            ->squish()
            ->ascii()
            ->replaceMatches('/[^\w\s]/u', '');
    }
}
