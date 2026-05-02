<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

setCors();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') jsonError('Method not allowed', 405);

$db   = getDb();
$stmt = $db->query("SELECT c.*, COUNT(p.id) AS post_count
                    FROM categories c
                    LEFT JOIN posts p ON p.category_id = c.id AND p.status = 'published'
                    GROUP BY c.id
                    ORDER BY c.sort_order ASC, c.name ASC");

jsonOut(['categories' => $stmt->fetchAll()]);
