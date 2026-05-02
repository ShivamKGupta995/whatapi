<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

setCors();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Method not allowed', 405);
if (!isLoggedIn()) jsonError('Unauthorized', 401);

if (empty($_FILES['image'])) jsonError('No file uploaded');

try {
    $url = uploadImage($_FILES['image']);
    jsonOut(['url' => $url]);
} catch (RuntimeException $e) {
    jsonError($e->getMessage());
}
