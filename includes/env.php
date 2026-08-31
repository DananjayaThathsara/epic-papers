<?php
/* ============================================
   ENV LOADER
   Reads a .env file (KEY=VALUE per line) from the project root into
   $_ENV / getenv(), if one exists. No external packages required.
   Lines starting with # are comments; blank lines are skipped.
   Values can optionally be wrapped in quotes.
   ============================================ */

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}

function load_env(string $path): void
{
    static $loaded = false;
    if ($loaded || !is_file($path)) return;
    $loaded = true;

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // strip matching surrounding quotes
        if (strlen($value) >= 2 && (
            ($value[0] === '"' && $value[-1] === '"') ||
            ($value[0] === "'" && $value[-1] === "'")
        )) {
            $value = substr($value, 1, -1);
        }

        if ($key === '') continue;
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

/** Reads an env var with a fallback default if it isn't set. */
function env(string $key, $default = null)
{
    $value = getenv($key);
    return $value !== false ? $value : $default;
}
