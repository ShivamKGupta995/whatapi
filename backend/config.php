<?php
/* Load .env from the same directory */
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line[0] === '#' || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}

function env(string $key, string $default = ''): string {
    return $_ENV[$key] ?? $default;
}

/* ── Database ─────────────────────────────────────────────── */
define('DB_HOST',    env('DB_HOST', 'localhost'));
define('DB_NAME',    env('DB_NAME', 'whatapi_blog'));
define('DB_USER',    env('DB_USER', ''));
define('DB_PASS',    env('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

/* ── Site ─────────────────────────────────────────────────── */
define('SITE_URL',     env('SITE_URL',     'http://localhost'));
define('FRONTEND_URL', env('FRONTEND_URL', 'http://localhost'));

/* ── Uploads ──────────────────────────────────────────────── */
define('UPLOAD_DIR',   __DIR__ . '/uploads/');
define('UPLOAD_URL',   SITE_URL . '/backend/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB

/* ── Admin ────────────────────────────────────────────────── */
define('ADMIN_EMAIL',  env('ADMIN_EMAIL', 'admin@yourdomain.com'));
define('ADMIN_PASS',   env('ADMIN_PASS',  ''));
define('SESSION_NAME', 'whatapi_admin');

/* ── Environment ──────────────────────────────────────────── */
define('DEBUG', env('DEBUG', 'false') === 'true');
