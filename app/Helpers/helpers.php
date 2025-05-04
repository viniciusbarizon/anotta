<?php

if (!function_exists('normalize_whitespace')) {
    /**
     * Normalizes whitespace in a given string by replacing multiple
     * consecutive whitespace characters with a single space and trimming
     * the result.
     *
     * @param string $value The input string to normalize.
     * @return string The normalized string with excess whitespace removed.
     */
    function normalize_whitespace(string $value): string
    {
        return mb_trim(preg_replace('/\s+/u', ' ', $value));
    }
}
