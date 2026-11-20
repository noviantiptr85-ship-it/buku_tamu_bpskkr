<?php
declare(strict_types=1);

$root = dirname(__DIR__);

/**
 * Pembaca .env sederhana (tanpa dependency).
 */
function env_load(string $file): array
{
    $vars = [];
    if (!is_file($file)) {
        return $vars;
    }

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $vars[$key] = $value;
        $_ENV[$key] = $value;
    }

    return $vars;
}

$env = env_load($root . '/.env');

define('ROOT_PATH', $root);
define('APP_PATH', $root . '/app');
define('VIEW_PATH', $root . '/views');
define('PUBLIC_PATH', $root . '/public');
define('STORAGE_PATH', $root . '/storage');

define('APP_NAME', $env['APP_NAME'] ?? 'Buku Tamu');
define('APP_ENV', $env['APP_ENV'] ?? 'production');
define('APP_DEBUG', APP_ENV === 'local');
define('APP_URL', rtrim($env['APP_URL'] ?? 'http://localhost:8000', '/'));

function env(string $key, ?string $default = null): ?string
{
    $value = $_ENV[$key] ?? null;
    return ($value === null || $value === '') ? $default : $value;
}

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Jakarta'));

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

if (PHP_SAPI !== 'cli' && session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_name('bukutamu_session');
    session_start();
}
