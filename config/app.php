<?php

/**
 * URL bazë e projektit (p.sh. /php-ecommerce-project-main)
 */
function base_url(string $path = ''): string
{
    static $base = null;

    if ($base === null) {
        $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
        $root = rtrim(str_replace('\\', '/', realpath(dirname(__DIR__))), '/');
        $base = ($docRoot && strpos($root, $docRoot) === 0)
            ? str_replace($docRoot, '', $root)
            : '';
        $base = rtrim($base, '/');
    }

    if ($path === '') {
        return $base === '' ? '' : $base;
    }

    return ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
}

function redirect_to(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}
