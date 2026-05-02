<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Dashboard – Whatapi Blog Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <meta name="robots" content="noindex, nofollow"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #F8FAFC; color: #0F172A; display: flex; min-height: 100vh; }

    /* Sidebar */
    .sidebar {
      width: 240px; background: #0B1613; color: rgba(255,255,255,.7);
      display: flex; flex-direction: column; flex-shrink: 0; position: fixed;
      top: 0; bottom: 0; left: 0; overflow-y: auto;
    }
    .sidebar-logo {
      display: flex; align-items: center; gap: 10px;
      padding: 24px 20px 20px; border-bottom: 1px solid rgba(255,255,255,.08);
      color: #fff; text-decoration: none;
    }
    .sidebar-logo-icon {
      width: 32px; height: 32px; background: #25D366; border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
    }
    .sidebar-logo span { font-weight: 600; font-size: .95rem; }
    .sidebar-label {
      font-size: .65rem; font-weight: 600; text-transform: uppercase;
      letter-spacing: .1em; color: rgba(255,255,255,.35); padding: 20px 20px 8px;
    }
    .sidebar-link {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 20px; font-size: .86rem; font-weight: 500;
      color: rgba(255,255,255,.6); text-decoration: none; border-radius: 0;
      transition: all .15s; border-left: 3px solid transparent;
    }
    .sidebar-link:hover { color: #fff; background: rgba(255,255,255,.05); }
    .sidebar-link.active { color: #25D366; border-left-color: #25D366; background: rgba(37,211,102,.08); }
    .sidebar-footer { margin-top: auto; padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.08); }
    .sidebar-footer a { font-size: .8rem; color: rgba(255,255,255,.4); text-decoration: none; }
    .sidebar-footer a:hover { color: rgba(255,255,255,.7); }

    /* Main */
    .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
    .topbar {
      background: #fff; border-bottom: 1px solid #E2E8F0;
      padding: 0 32px; height: 64px;
      display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 10;
    }
    .topbar h1 { font-size: 1rem; font-weight: 600; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: 8px; font-family: inherit; font-size: .84rem; font-weight: 500; cursor: pointer; text-decoration: none; border: none; transition: all .15s; }
    .btn-primary { background: #25D366; color: #fff; }
    .btn-primary:hover { background: #1DA851; }
    .btn-outline { border: 1.5px solid #E2E8F0; background: #fff; color: #475569; }
    .btn-outline:hover { border-color: #25D366; color: #128C7E; }
    .btn-danger  { background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; }
    .btn-danger:hover { background: #EF4444; color: #fff; }
    .btn-sm { padding: 6px 12px; font-size: .78rem; }

    /* Content */
    .content { padding: 32px; flex: 1; }

    /* Stats */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 32px; }
    .stat-card { background: #fff; border: 1px solid #E2E8F0; border-radius: 10px; padding: 20px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
    .stat-card .s-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: #64748B; margin-bottom: 8px; }
    .stat-card .s-val   { font-size: 1.8rem; font-weight: 600; color: #0F172A; letter-spacing: -.02em; }
    .stat-card .s-sub   { font-size: .76rem; color: #94A3B8; margin-top: 4px; }
    .stat-card.green .s-val { color: #25D366; }

    /* Filter bar */
    .filter-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-bar input[type=search] {
      flex: 1; min-width: 200px; padding: 9px 14px; font-family: inherit;
      font-size: .86rem; border: 1.5px solid #E2E8F0; border-radius: 8px;
      color: #0F172A; outline: none;
    }
    .filter-bar input:focus { border-color: #25D366; }
    .filter-bar select {
      padding: 9px 14px; font-family: inherit; font-size: .86rem;
      border: 1.5px solid #E2E8F0; border-radius: 8px; color: #475569;
      background: #fff; outline: none; cursor: pointer;
    }

    /* Table */
    .table-wrap { background: #fff; border: 1px solid #E2E8F0; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
    table { width: 100%; border-collapse: collapse; }
    thead th {
      background: #F8FAFC; padding: 12px 18px; text-align: left;
      font-size: .76rem; font-weight: 600; text-transform: uppercase;
      letter-spacing: .06em; color: #64748B; border-bottom: 1px solid #E2E8F0;
      white-space: nowrap;
    }
    tbody td { padding: 14px 18px; border-bottom: 1px solid #F1F5F9; font-size: .86rem; color: #475569; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #FAFAFA; }
    .post-title-cell { max-width: 320px; }
    .post-title-cell strong { display: block; font-size: .9rem; color: #0F172A; font-weight: 600; margin-bottom: 2px; }
    .post-title-cell span { font-size: .76rem; color: #94A3B8; }
    .badge {
      display: inline-block; padding: 3px 9px; border-radius: 12px;
      font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
    }
    .badge-published { background: rgba(37,211,102,.12); color: #0e6e3e; }
    .badge-draft     { background: rgba(241,245,249,1); color: #64748B; }
    .actions { display: flex; align-items: center; gap: 6px; }
    .empty-state { text-align: center; padding: 60px 24px; color: #94A3B8; }
    .empty-state p { font-size: 2rem; margin-bottom: 12px; }
  </style>
</head>
<body>
<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$db   = getDb();
$filter = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$where = ['1=1'];
$params = [];
if ($filter !== 'all') { $where[] = 'p.status=:st'; $params[':st'] = $filter; }
if ($search) { $where[] = 'p.title LIKE :q'; $params[':q'] = '%'.$search.'%'; }
$cond = implode(' AND ', $where);

$posts = $db->prepare("SELECT p.id, p.title, p.slug, p.status, p.author_name, p.published_at, p.read_time, c.name AS category
                        FROM posts p LEFT JOIN categories c ON p.category_id=c.id
                        WHERE $cond ORDER BY p.id DESC");
$posts->execute($params);
$posts = $posts->fetchAll();

$totals = $db->query("SELECT status, COUNT(*) AS n FROM posts GROUP BY status")->fetchAll();
$counts = ['all' => 0, 'published' => 0, 'draft' => 0];
foreach ($totals as $r) { $counts[$r['status']] = $r['n']; $counts['all'] += $r['n']; }
?>

<!-- Sidebar -->
<aside class="sidebar">
  <a href="dashboard.php" class="sidebar-logo">
    <div class="sidebar-logo-icon">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </div>
    <span>Whatapi Blog</span>
  </a>
  <div class="sidebar-label">Content</div>
  <a href="dashboard.php" class="sidebar-link active">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    All Posts
  </a>
  <a href="editor.php" class="sidebar-link">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Post
  </a>
  <div class="sidebar-label">Tools</div>
  <a href="../api/sitemap.php" target="_blank" class="sidebar-link">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
    View Sitemap
  </a>
  <div class="sidebar-footer">
    <a href="logout.php">← Logout</a>
  </div>
</aside>

<!-- Main -->
<div class="main">
  <div class="topbar">
    <h1>Blog Posts</h1>
    <div class="topbar-right">
      <a href="editor.php" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Post
      </a>
    </div>
  </div>

  <div class="content">
    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="s-label">Total Posts</div>
        <div class="s-val"><?= $counts['all'] ?></div>
      </div>
      <div class="stat-card green">
        <div class="s-label">Published</div>
        <div class="s-val"><?= $counts['published'] ?></div>
      </div>
      <div class="stat-card">
        <div class="s-label">Drafts</div>
        <div class="s-val"><?= $counts['draft'] ?></div>
      </div>
      <div class="stat-card">
        <div class="s-label">Indexed Pages</div>
        <div class="s-val"><?= $counts['published'] ?></div>
        <div class="s-sub">In sitemap.xml</div>
      </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="filter-bar">
      <input type="search" name="q" placeholder="Search posts…" value="<?= htmlspecialchars($search) ?>"/>
      <select name="status" onchange="this.form.submit()">
        <option value="all"       <?= $filter==='all'       ?'selected':''?>>All (<?= $counts['all'] ?>)</option>
        <option value="published" <?= $filter==='published' ?'selected':''?>>Published (<?= $counts['published'] ?>)</option>
        <option value="draft"     <?= $filter==='draft'     ?'selected':''?>>Drafts (<?= $counts['draft'] ?>)</option>
      </select>
      <button type="submit" class="btn btn-outline">Search</button>
    </form>

    <!-- Table -->
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Author</th>
            <th>Published</th>
            <th>Read</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$posts): ?>
            <tr><td colspan="7">
              <div class="empty-state"><p>📝</p>No posts yet. <a href="editor.php" style="color:#128C7E">Create one →</a></div>
            </td></tr>
          <?php else: ?>
            <?php foreach ($posts as $p): ?>
            <tr>
              <td class="post-title-cell">
                <strong><?= htmlspecialchars($p['title']) ?></strong>
                <span>/blog/post.html?slug=<?= htmlspecialchars($p['slug']) ?></span>
              </td>
              <td><?= htmlspecialchars($p['category'] ?? '—') ?></td>
              <td>
                <span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
              </td>
              <td><?= htmlspecialchars($p['author_name']) ?></td>
              <td><?= $p['published_at'] ? date('d M Y', strtotime($p['published_at'])) : '—' ?></td>
              <td><?= $p['read_time'] ?> min</td>
              <td>
                <div class="actions">
                  <a href="editor.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                  <a href="/blog/post.html?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
                  <form method="POST" action="delete.php" onsubmit="return confirm('Delete this post? This cannot be undone.')">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>"/>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
