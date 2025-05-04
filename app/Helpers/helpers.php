<?php

if (!function_exists('normalize_name')) {
    function normalize_name(string $name): string
    {
        return (string) str($name)
            ->lower()
            ->squish()
            ->ascii();
    }
}
