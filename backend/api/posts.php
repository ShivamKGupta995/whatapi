<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

setCors();
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

/* ── GET: list posts ───────────────────────────────────────── */
if ($method === 'GET') {
    $db       = getDb();
    $status   = $_GET['status']   ?? 'published';
    $category = $_GET['category'] ?? '';
    $search   = $_GET['search']   ?? '';
    $page     = max(1, (int)($_GET['page']  ?? 1));
    $limit    = min(50, max(1, (int)($_GET['limit'] ?? 12)));
    $offset   = ($page - 1) * $limit;

    $where  = ['1=1'];
    $params = [];

    if ($status !== 'all') {
        $where[]  = 'p.status = :status';
        $params[':status'] = $status;
    }
    if ($category !== '' && $category !== 'all') {
        $where[]  = 'c.name = :cat';
        $params[':cat'] = $category;
    }
    if ($search !== '') {
        $where[]  = '(p.title LIKE :q OR p.excerpt LIKE :q2)';
        $params[':q']  = '%' . $search . '%';
        $params[':q2'] = '%' . $search . '%';
    }

    $cond = implode(' AND ', $where);

    $countSql = "SELECT COUNT(*) FROM posts p
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE $cond";
    $total = (int) $db->prepare($countSql)->execute($params) ? $db->prepare($countSql)->fetchColumn() : 0;
    $stmt  = $db->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.thumbnail_url,
                   p.author_name, p.author_role, p.published_at, p.read_time,
                   p.status, c.name AS category
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE $cond
            ORDER BY p.published_at DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();

    jsonOut([
        'posts'      => $posts,
        'total'      => $total,
        'page'       => $page,
        'limit'      => $limit,
        'totalPages' => (int) ceil($total / $limit),
    ]);
}

/* ── POST: create post (admin only) ───────────────────────── */
if ($method === 'POST') {
    if (!isLoggedIn()) jsonError('Unauthorized', 401);

    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body) jsonError('Invalid JSON body');

    $required = ['title', 'content', 'category_id'];
    foreach ($required as $f) {
        if (empty($body[$f])) jsonError("Field '$f' is required");
    }

    $db      = getDb();
    $title   = trim($body['title']);
    $slug    = !empty($body['slug']) ? trim($body['slug']) : makeSlug($title);

    /* Ensure unique slug */
    $base = $slug; $i = 1;
    while ($db->prepare('SELECT id FROM posts WHERE slug=?')->execute([$slug]) &&
           $db->prepare('SELECT id FROM posts WHERE slug=?')->fetchColumn()) {
        $stmt = $db->prepare('SELECT id FROM posts WHERE slug=?');
        $stmt->execute([$slug]);
        if (!$stmt->fetchColumn()) break;
        $slug = $base . '-' . $i++;
    }

    $content   = $body['content'];
    $readTime  = calcReadTime($content);
    $status    = in_array($body['status'] ?? '', ['draft','published']) ? $body['status'] : 'draft';
    $pubAt     = $status === 'published' ? ($body['published_at'] ?? date('Y-m-d H:i:s')) : null;

    $stmt = $db->prepare("INSERT INTO posts
        (title, slug, content, excerpt, thumbnail_url, category_id,
         author_name, author_role, author_bio, status,
         meta_title, meta_description, tags, read_time, published_at)
        VALUES
        (:title,:slug,:content,:excerpt,:thumb,:cat,
         :aname,:arole,:abio,:status,
         :mtitle,:mdesc,:tags,:rt,:pubat)");

    $stmt->execute([
        ':title'   => $title,
        ':slug'    => $slug,
        ':content' => $content,
        ':excerpt' => trim($body['excerpt'] ?? ''),
        ':thumb'   => $body['thumbnail_url'] ?? null,
        ':cat'     => (int)$body['category_id'],
        ':aname'   => trim($body['author_name'] ?? 'Whatapi Team'),
        ':arole'   => trim($body['author_role'] ?? ''),
        ':abio'    => trim($body['author_bio']  ?? ''),
        ':status'  => $status,
        ':mtitle'  => trim($body['meta_title'] ?? $title),
        ':mdesc'   => trim($body['meta_description'] ?? $body['excerpt'] ?? ''),
        ':tags'    => trim($body['tags'] ?? ''),
        ':rt'      => $readTime,
        ':pubat'   => $pubAt,
    ]);

    jsonOut(['success' => true, 'id' => (int)$db->lastInsertId(), 'slug' => $slug], 201);
}

/* ── PUT: update post ──────────────────────────────────────── */
if ($method === 'PUT') {
    if (!isLoggedIn()) jsonError('Unauthorized', 401);

    $id   = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('Missing post id');

    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body) jsonError('Invalid JSON body');

    $db      = getDb();
    $content = $body['content'] ?? '';

    $stmt = $db->prepare("UPDATE posts SET
        title=:title, content=:content, excerpt=:excerpt,
        thumbnail_url=:thumb, category_id=:cat,
        author_name=:aname, author_role=:arole, author_bio=:abio,
        status=:status, meta_title=:mtitle, meta_description=:mdesc,
        tags=:tags, read_time=:rt,
        published_at = CASE WHEN :status2='published' AND published_at IS NULL THEN NOW() ELSE published_at END
        WHERE id=:id");

    $status = in_array($body['status'] ?? '', ['draft','published']) ? $body['status'] : 'draft';

    $stmt->execute([
        ':title'   => trim($body['title'] ?? ''),
        ':content' => $content,
        ':excerpt' => trim($body['excerpt'] ?? ''),
        ':thumb'   => $body['thumbnail_url'] ?? null,
        ':cat'     => (int)($body['category_id'] ?? 1),
        ':aname'   => trim($body['author_name'] ?? 'Whatapi Team'),
        ':arole'   => trim($body['author_role'] ?? ''),
        ':abio'    => trim($body['author_bio']  ?? ''),
        ':status'  => $status,
        ':status2' => $status,
        ':mtitle'  => trim($body['meta_title'] ?? $body['title'] ?? ''),
        ':mdesc'   => trim($body['meta_description'] ?? ''),
        ':tags'    => trim($body['tags'] ?? ''),
        ':rt'      => calcReadTime($content),
        ':id'      => $id,
    ]);

    jsonOut(['success' => true]);
}

/* ── DELETE ───────────────────────────────────────────────── */
if ($method === 'DELETE') {
    if (!isLoggedIn()) jsonError('Unauthorized', 401);
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('Missing post id');
    $db = getDb();
    $db->prepare('DELETE FROM posts WHERE id=?')->execute([$id]);
    jsonOut(['success' => true]);
}

jsonError('Method not allowed', 405);
