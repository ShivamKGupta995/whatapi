<?php
/* ── Database ─────────────────────────────────────────────── */
define('DB_HOST', 'localhost');
define('DB_NAME', 'whatapi_blog');   // Create this DB in Hostinger hPanel
define('DB_USER', 'your_db_user');   // From hPanel → Databases
define('DB_PASS', 'your_db_pass');   // From hPanel → Databases
define('DB_CHARSET', 'utf8mb4');

/* ── Site ─────────────────────────────────────────────────── */
define('SITE_URL',   'https://yourdomain.com');   // Your Hostinger domain
define('FRONTEND_URL', 'https://yourdomain.com'); // Where your static site is hosted

/* ── Uploads ──────────────────────────────────────────────── */
define('UPLOAD_DIR',  __DIR__ . '/uploads/');
define('UPLOAD_URL',  SITE_URL . '/backend/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB

/* ── Admin ────────────────────────────────────────────────── */
define('ADMIN_EMAIL', 'admin@yourdomain.com');
define('ADMIN_PASS',  'change_this_password');  // Change before going live!
define('SESSION_NAME', 'whatapi_admin');

/* ── Environment ──────────────────────────────────────────── */
define('DEBUG', false); // Set true only during local development
