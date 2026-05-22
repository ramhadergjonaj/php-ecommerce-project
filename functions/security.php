<?php

/**
 * Escape për output (mbrojtje XSS).
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function cleanInput(string $data): string
{
    return trim(stripslashes($data));
}
