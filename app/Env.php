<?php
namespace App;

class Env {
    /**
     * Load simple KEY=VALUE pairs from a .env file into $_ENV/$_SERVER and getenv().
     * Lines starting with # are ignored. Quotes around the value are stripped.
     */
    public static function load(string $path): void {
        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Split "KEY=VALUE" at the first "=" only
            [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
            $name  = trim($name);
            $value = trim($value);

            // Strip surrounding quotes if present
            $value = trim($value, "\"'");

            if ($name !== '') {
                $_ENV[$name]    = $value;
                $_SERVER[$name] = $value;
                // Set process env var
                putenv($name . '=' . $value);
            }
        }
    }

    /**
     * Get a previously loaded env var (or from getenv()) with a default.
     */
    public static function get(string $key, $default = null) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?? $default;
    }
}
