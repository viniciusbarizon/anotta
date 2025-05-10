<?php

declare(strict_types=1);

if (! function_exists('normalize_name')) {
    /**
     * Normalize a given name string by performing the following transformations:
     * - Converts the string to lowercase.
     * - Removes extra whitespace and trims the string.
     * - Converts the string to ASCII format.
     * - Removes any non-word or non-space characters using a regular expression.
     *
     * @param string $name The input name to be normalized.
     * @return string The normalized name.
     */
    function normalize_name(string $name): string
    {
        return (string) str($name)
            ->lower()
            ->squish()
            ->ascii()
            ->replaceMatches('/[^\w\s]/u', '');
    }
}
