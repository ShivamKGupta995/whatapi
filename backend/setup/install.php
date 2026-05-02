<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Whatapi Blog – Setup</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <meta name="robots" content="noindex, nofollow"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #F8FAFC; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
    .card { background: #fff; border-radius: 16px; padding: 44px 40px; max-width: 560px; width: 100%; box-shadow: 0 8px 40px rgba(0,0,0,.1); }
    .logo { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
    .logo-icon { width: 36px; height: 36px; background: #25D366; border-radius: 9px; display: flex; align-items: center; justify-content: center; }
    .logo span { font-size: 1.1rem; font-weight: 600; }
    h1 { font-size: 1.4rem; margin-bottom: 8px; }
    p  { color: #64748B; font-size: .9rem; margin-bottom: 24px; line-height: 1.7; }
    .step { display: flex; gap: 14px; margin-bottom: 14px; padding: 14px 16px; border-radius: 10px; background: #F8FAFC; border: 1px solid #E2E8F0; }
    .step-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(37,211,102,.12); color: #128C7E; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .9rem; flex-shrink: 0; }
    .step-icon.ok  { background: rgba(37,211,102,.15); color: #15803D; }
    .step-icon.err { background: rgba(239,68,68,.12); color: #991B1B; }
    .step-text strong { display: block; font-size: .88rem; margin-bottom: 3px; }
    .step-text span   { font-size: .8rem; color: #64748B; }
    .btn { display: inline-block; margin-top: 24px; padding: 13px 28px; background: #25D366; color: #fff; border-radius: 8px; font-family: inherit; font-size: .92rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; transition: background .2s; }
    .btn:hover { background: #1DA851; }
    .btn-outline { background: #fff; color: #128C7E; border: 1.5px solid #25D366; margin-left: 10px; }
    .warn { background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 8px; padding: 12px 16px; font-size: .82rem; color: #92400E; margin-top: 20px; }
  </style>
</head>
<body>
<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';

$steps   = [];
$success = true;

/* Step 1: DB Connection */
try {
    $db = getDb();
    $steps[] = ['ok', 'Database connection', 'Connected to ' . DB_NAME . ' on ' . DB_HOST];
} catch (Exception $e) {
    $steps[] = ['err', 'Database connection', $e->getMessage()];
    $success = false;
}

if ($success) {
    /* Step 2: Create tables */
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS categories (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(100) NOT NULL,
            slug        VARCHAR(100) NOT NULL UNIQUE,
            color       VARCHAR(20)  DEFAULT '#25D366',
            sort_order  INT          DEFAULT 0,
            created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->exec("CREATE TABLE IF NOT EXISTS posts (
            id                  INT AUTO_INCREMENT PRIMARY KEY,
            title               VARCHAR(255)    NOT NULL,
            slug                VARCHAR(255)    NOT NULL UNIQUE,
            content             LONGTEXT,
            excerpt             TEXT,
            thumbnail_url       VARCHAR(500),
            category_id         INT             DEFAULT NULL,
            author_name         VARCHAR(100)    DEFAULT 'Whatapi Team',
            author_role         VARCHAR(150),
            author_bio          TEXT,
            status              ENUM('draft','published') DEFAULT 'draft',
            meta_title          VARCHAR(255),
            meta_description    TEXT,
            tags                TEXT,
            read_time           INT             DEFAULT 5,
            published_at        DATETIME        DEFAULT NULL,
            created_at          TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
            updated_at          TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            INDEX idx_slug   (slug),
            INDEX idx_status (status),
            INDEX idx_pubat  (published_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $steps[] = ['ok', 'Database tables created', 'posts, categories tables ready'];
    } catch (Exception $e) {
        $steps[] = ['err', 'Create tables', $e->getMessage()];
        $success = false;
    }

    /* Step 3: Seed default categories */
    try {
        $cats = [
            ['WhatsApp Marketing', 'whatsapp-marketing', '#25D366', 1],
            ['Business Tips',      'business-tips',      '#6366F1', 2],
            ['Case Studies',       'case-studies',       '#F59E0B', 3],
            ['How-To Guides',      'how-to-guides',      '#EC4899', 4],
            ['Platform Updates',   'platform-updates',   '#128C7E', 5],
            ['E-commerce',         'ecommerce',          '#EF4444', 6],
        ];
        $ins = $db->prepare("INSERT IGNORE INTO categories (name,slug,color,sort_order) VALUES (?,?,?,?)");
        foreach ($cats as $c) $ins->execute($c);
        $steps[] = ['ok', 'Default categories seeded', implode(', ', array_column($cats, 0))];
    } catch (Exception $e) {
        $steps[] = ['err', 'Seed categories', $e->getMessage()];
    }

    /* Step 4: Upload directory */
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $htaccess = $uploadDir . '.htaccess';
    if (!file_exists($htaccess)) file_put_contents($htaccess, "Options -Indexes\n");
    $steps[] = is_writable($uploadDir)
        ? ['ok',  'Uploads directory', 'Created and writable at /backend/uploads/']
        : ['err', 'Uploads directory', 'Not writable — check permissions (chmod 755)'];
}
?>

<div class="card">
  <div class="logo">
    <div class="logo-icon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </div>
    <span>Whatapi Blog Setup</span>
  </div>

  <h1><?= $success ? '✅ Installation Complete!' : '❌ Setup Failed' ?></h1>
  <p><?= $success
    ? 'Your blog database is ready. Go to the admin panel to create your first post.'
    : 'One or more steps failed. Check your config.php and try again.' ?></p>

  <?php foreach ($steps as [$status, $title, $detail]): ?>
    <div class="step">
      <div class="step-icon <?= $status ?>"><?= $status === 'ok' ? '✓' : '✗' ?></div>
      <div class="step-text">
        <strong><?= htmlspecialchars($title) ?></strong>
        <span><?= htmlspecialchars($detail) ?></span>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if ($success): ?>
    <a href="../admin/login.php" class="btn">Go to Admin Panel →</a>
    <div class="warn">
      <strong>Security:</strong> Delete or protect this file (<code>setup/install.php</code>)
      after installation. It exposes your database info if left accessible.
    </div>
  <?php else: ?>
    <a href="install.php" class="btn">Retry Setup</a>
  <?php endif; ?>
</div>

</body>
</html>
