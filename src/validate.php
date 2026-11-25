<?php
declare(strict_types=1);

/** True if the string has at least one non-whitespace character. */
function validate_required(string $v): bool
{
    return trim($v) !== '';
}

/** True if the string is a syntactically valid email address. */
function validate_email(string $v): bool
{
    return filter_var($v, FILTER_VALIDATE_EMAIL) !== false;
}

/** True if the string length (in characters) is within [$min, $max]. */
function validate_length(string $v, int $min, int $max): bool
{
    $len = function_exists('mb_strlen') ? mb_strlen($v, 'UTF-8') : strlen($v);
    return $len >= $min && $len <= $max;
}
