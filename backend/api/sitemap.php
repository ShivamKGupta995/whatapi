<?php
/* Generates /sitemap.xml for all published blog posts great for SEO */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/xml; charset=utf-8');

$db   = getDb();
$stmt = $db->query("SELECT slug, published_at, updated_at FROM posts WHERE status='published' ORDER BY published_at DESC");
$posts = $stmt->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

/* Static pages */
$staticPages = [
    ['url' => FRONTEND_URL . '/',                    'priority' => '1.0', 'freq' => 'weekly'],
    ['url' => FRONTEND_URL . '/blog/',               'priority' => '0.9', 'freq' => 'daily'],
    ['url' => FRONTEND_URL . '/pages/pricing.html',  'priority' => '0.8', 'freq' => 'monthly'],
    ['url' => FRONTEND_URL . '/pages/contact.html',  'priority' => '0.7', 'freq' => 'monthly'],
    ['url' => FRONTEND_URL . '/pages/partner.html',  'priority' => '0.6', 'freq' => 'monthly'],
];

foreach ($staticPages as $p) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($p['url']) . "</loc>\n";
    echo "    <changefreq>{$p['freq']}</changefreq>\n";
    echo "    <priority>{$p['priority']}</priority>\n";
    echo "  </url>\n";
}

/* Blog posts */
foreach ($posts as $post) {
    $url     = htmlspecialchars(FRONTEND_URL . '/blog/post.html?slug=' . $post['slug']);
    $lastmod = date('Y-m-d', strtotime($post['updated_at'] ?: $post['published_at']));
    echo "  <url>\n";
    echo "    <loc>$url</loc>\n";
    echo "    <lastmod>$lastmod</lastmod>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "    <priority>0.7</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>';
