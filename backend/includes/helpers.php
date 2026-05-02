<?php
require_once __DIR__ . '/../config.php';

/* ── CORS ─────────────────────────────────────────────────── */
function setCors(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    // Allow your frontend domain and localhost for dev
    $allowed = [FRONTEND_URL, 'http://localhost', 'http://127.0.0.1'];

    if (in_array($origin, $allowed, true) || str_starts_with($origin, 'http://localhost:')) {
        header("Access-Control-Allow-Origin: $origin");
    } else {
        header('Access-Control-Allow-Origin: ' . FRONTEND_URL);
    }

    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/* ── JSON Response ────────────────────────────────────────── */
function jsonOut(array $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function jsonError(string $msg, int $code = 400): never {
    jsonOut(['error' => $msg], $code);
}

/* ── Slug ─────────────────────────────────────────────────── */
function makeSlug(string $title): string {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

/* ── Read Time ─────────────────────────────────────────────── */
function calcReadTime(string $content): int {
    $words = str_word_count(strip_tags($content));
    return max(1, (int) ceil($words / 200));
}

/* ── Sanitize ──────────────────────────────────────────────── */
function clean(string $val): string {
    return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
}

/* ── Image Upload ──────────────────────────────────────────── */
function uploadImage(array $file): string {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!in_array($file['type'], $allowed, true)) {
        throw new RuntimeException('Invalid file type. Only JPG, PNG, WebP, GIF allowed.');
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new RuntimeException('File too large. Max 5 MB.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    $dest     = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Failed to save uploaded file.');
    }

    return UPLOAD_URL . $filename;
}
