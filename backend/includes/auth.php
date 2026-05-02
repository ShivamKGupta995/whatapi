<?php
require_once __DIR__ . '/../config.php';

function startAdminSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function isLoggedIn(): bool {
    startAdminSession();
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /backend/admin/login.php');
        exit;
    }
}

function attemptLogin(string $email, string $password): bool {
    if ($email !== ADMIN_EMAIL) return false;
    if (!password_verify($password, password_hash(ADMIN_PASS, PASSWORD_DEFAULT))) {
        /* Direct compare as fallback when ADMIN_PASS is stored as plaintext */
        if ($password !== ADMIN_PASS) return false;
    }
    startAdminSession();
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_email']     = $email;
    return true;
}

function logout(): void {
    startAdminSession();
    $_SESSION = [];
    session_destroy();
}
