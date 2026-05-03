<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config.php';
requireLogin();

$postId = (int)($_GET['id'] ?? 0);
$post   = null;
$isEdit = false;

if ($postId) {
    $stmt = getDb()->prepare('SELECT p.*, c.name AS category_name FROM posts p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id=?');
    $stmt->execute([$postId]);
    $post   = $stmt->fetch();
    $isEdit = (bool)$post;
}

$cats = getDb()->query('SELECT * FROM categories ORDER BY sort_order, name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title id="pageTitle">New Post – Whatapi Blog Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <!-- Quill Rich Text Editor -->
  <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet"/>
  <meta name="robots" content="noindex, nofollow"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #F8FAFC; color: #0F172A; display: flex; min-height: 100vh; }

    /* Sidebar — same as dashboard */
    .sidebar {
      width: 240px; background: #0B1613; color: rgba(255,255,255,.7);
      display: flex; flex-direction: column; flex-shrink: 0;
      position: fixed; top: 0; bottom: 0; left: 0; overflow-y: auto;
    }
    .sidebar-logo { display: flex; align-items: center; gap: 10px; padding: 24px 20px 20px; border-bottom: 1px solid rgba(255,255,255,.08); color: #fff; text-decoration: none; }
    .sidebar-logo-icon { width: 32px; height: 32px; background: #25D366; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .sidebar-logo span { font-weight: 600; font-size: .95rem; }
    .sidebar-label { font-size: .65rem; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.35); padding: 20px 20px 8px; }
    .sidebar-link { display: flex; align-items: center; gap: 10px; padding: 10px 20px; font-size: .86rem; font-weight: 500; color: rgba(255,255,255,.6); text-decoration: none; border-left: 3px solid transparent; transition: all .15s; }
    .sidebar-link:hover { color: #fff; background: rgba(255,255,255,.05); }
    .sidebar-link.active { color: #25D366; border-left-color: #25D366; background: rgba(37,211,102,.08); }
    .sidebar-footer { margin-top: auto; padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.08); }
    .sidebar-footer a { font-size: .8rem; color: rgba(255,255,255,.4); text-decoration: none; }

    /* Main */
    .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; }
    .topbar { background: #fff; border-bottom: 1px solid #E2E8F0; padding: 0 32px; height: 64px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 10; }
    .topbar h1 { font-size: 1rem; font-weight: 600; }
    .topbar-right { display: flex; align-items: center; gap: 10px; }
    .btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: 8px; font-family: inherit; font-size: .84rem; font-weight: 500; cursor: pointer; text-decoration: none; border: none; transition: all .15s; }
    .btn-primary { background: #25D366; color: #fff; }
    .btn-primary:hover { background: #1DA851; }
    .btn-outline { border: 1.5px solid #E2E8F0; background: #fff; color: #475569; }
    .btn-outline:hover { border-color: #25D366; color: #128C7E; }
    .btn-ghost { background: transparent; color: #475569; border: 1.5px solid #E2E8F0; }
    .btn-ghost:hover { background: #F8FAFC; }

    /* Editor layout */
    .editor-wrap { display: grid; grid-template-columns: 1fr 320px; gap: 0; flex: 1; }
    .editor-main { padding: 32px; overflow-y: auto; border-right: 1px solid #E2E8F0; }
    .editor-sidebar { padding: 24px 20px; background: #fff; overflow-y: auto; }

    /* Form elements */
    .form-group { margin-bottom: 20px; }
    label { display: block; font-size: .78rem; font-weight: 600; color: #374151; margin-bottom: 7px; letter-spacing: .02em; }
    input[type=text], input[type=email], select, textarea {
      width: 100%; padding: 11px 14px; font-family: inherit; font-size: .9rem;
      border: 1.5px solid #E2E8F0; border-radius: 8px; color: #0F172A; outline: none;
      transition: border-color .2s; background: #fff;
    }
    input:focus, select:focus, textarea:focus { border-color: #25D366; box-shadow: 0 0 0 3px rgba(37,211,102,.1); }
    textarea { resize: vertical; min-height: 80px; line-height: 1.65; }
    small { font-size: .74rem; color: #94A3B8; margin-top: 5px; display: block; }

    /* Title input */
    .title-input {
      font-size: 1.5rem; font-weight: 600; border: none; border-bottom: 2px solid #E2E8F0;
      border-radius: 0; padding: 8px 0; width: 100%;
      font-family: inherit; color: #0F172A; outline: none; background: transparent;
      letter-spacing: -.015em;
    }
    .title-input:focus { border-bottom-color: #25D366; box-shadow: none; }
    .title-input::placeholder { color: #CBD5E1; }

    /* Quill */
    .quill-wrap { margin-top: 24px; }
    .quill-label-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .quill-label { font-size: .78rem; font-weight: 600; color: #374151; letter-spacing: .02em; }
    .upload-file-btn { display: inline-flex; align-items: center; gap: 5px; font-size: .76rem; font-weight: 500; color: #128C7E; cursor: pointer; padding: 4px 10px; border: 1px solid #25D366; border-radius: 6px; transition: background .15s; }
    .upload-file-btn:hover { background: rgba(37,211,102,.08); }
    #quillEditor { min-height: 420px; font-family: 'Inter', sans-serif; font-size: .95rem; line-height: 1.75; }
    .ql-toolbar { border-radius: 8px 8px 0 0; border-color: #E2E8F0 !important; background: #F8FAFC; }
    .ql-container { border-radius: 0 0 8px 8px; border-color: #E2E8F0 !important; }
    .ql-editor { min-height: 400px; }

    /* Sidebar card */
    .sidebar-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 16px; margin-bottom: 16px; }
    .sidebar-card h3 { font-size: .8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: #64748B; margin-bottom: 14px; }

    /* Thumbnail upload */
    .thumb-drop {
      border: 2px dashed #E2E8F0; border-radius: 8px; padding: 20px;
      text-align: center; cursor: pointer; transition: border-color .2s;
      background: #fff;
    }
    .thumb-drop:hover { border-color: #25D366; }
    .thumb-drop.has-image { padding: 0; overflow: hidden; border-style: solid; border-color: #25D366; }
    .thumb-drop img { width: 100%; display: block; border-radius: 6px; }
    .thumb-drop p { font-size: .8rem; color: #94A3B8; margin-top: 8px; }
    .thumb-drop input[type=file] { display: none; }

    /* SEO preview */
    .seo-preview { background: #fff; border: 1px solid #E2E8F0; border-radius: 8px; padding: 14px; margin-top: 12px; }
    .seo-url   { font-size: .7rem; color: #15803D; margin-bottom: 4px; }
    .seo-title { font-size: .88rem; color: #1a0dab; font-weight: 500; margin-bottom: 3px; line-height: 1.3; }
    .seo-desc  { font-size: .78rem; color: #545454; line-height: 1.5; }

    /* Toast */
    .toast {
      position: fixed; bottom: 28px; right: 28px; z-index: 9999;
      background: #0F172A; color: #fff; padding: 12px 20px;
      border-radius: 10px; font-size: .86rem; font-weight: 500;
      box-shadow: 0 8px 24px rgba(0,0,0,.2); opacity: 0;
      transform: translateY(8px); transition: all .25s;
    }
    .toast.show { opacity: 1; transform: translateY(0); }
    .toast.success { background: #15803D; }
    .toast.error   { background: #991B1B; }
  </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
  <a href="dashboard.php" class="sidebar-logo">
    <div class="sidebar-logo-icon">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </div>
    <span>Whatapi Blog</span>
  </a>
  <div class="sidebar-label">Content</div>
  <a href="dashboard.php" class="sidebar-link">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    All Posts
  </a>
  <a href="editor.php" class="sidebar-link active">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Post
  </a>
  <div class="sidebar-footer">
    <a href="logout.php">← Logout</a>
  </div>
</aside>

<!-- Main -->
<div class="main">
  <div class="topbar">
    <h1><?= $isEdit ? 'Edit Post' : 'New Post' ?></h1>
    <div class="topbar-right">
      <button class="btn btn-ghost" onclick="saveDraft()">Save Draft</button>
      <button class="btn btn-primary" onclick="publishPost()">
        <?= $isEdit ? 'Update Post' : 'Publish Post' ?> →
      </button>
    </div>
  </div>

  <div class="editor-wrap">
    <!-- Left: main editor -->
    <div class="editor-main">
      <input type="text" class="title-input" id="postTitle"
             placeholder="Post title…"
             value="<?= htmlspecialchars($post['title'] ?? '') ?>"/>

      <div class="form-group" style="margin-top:16px;">
        <label for="postExcerpt">Excerpt / Meta Description</label>
        <textarea id="postExcerpt" rows="3" placeholder="Brief summary (used for SEO and blog card preview)"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
      </div>

      <div class="quill-wrap">
        <div class="quill-label-row">
          <span class="quill-label">Content</span>
          <label class="upload-file-btn" title="Import HTML or Markdown file">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Import .html / .md
            <input type="file" id="contentFileInput" accept=".html,.htm,.md,.markdown" style="display:none"/>
          </label>
        </div>
        <div id="quillEditor"><?= $post['content'] ?? '' ?></div>
        <input type="hidden" id="postContent" />
      </div>
    </div>

    <!-- Right: sidebar -->
    <div class="editor-sidebar">

      <!-- Publish settings -->
      <div class="sidebar-card">
        <h3>Publish</h3>
        <div class="form-group">
          <label for="postStatus">Status</label>
          <select id="postStatus">
            <option value="draft"     <?= ($post['status']??'draft')==='draft'     ?'selected':''?>>Draft</option>
            <option value="published" <?= ($post['status']??'')==='published'?'selected':''?>>Published</option>
          </select>
        </div>
        <div class="form-group">
          <label for="postCategory">Category</label>
          <select id="postCategory">
            <?php foreach ($cats as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($post['category_id']??'')==$c['id']?'selected':''?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="postSlug">URL Slug</label>
          <input type="text" id="postSlug" placeholder="auto-generated-from-title"
                 value="<?= htmlspecialchars($post['slug'] ?? '') ?>"/>
          <small>Leave blank to auto-generate from title</small>
        </div>
      </div>

      <!-- Author -->
      <div class="sidebar-card">
        <h3>Author</h3>
        <div class="form-group">
          <label for="authorName">Name</label>
          <input type="text" id="authorName" placeholder="e.g. Priya Sharma"
                 value="<?= htmlspecialchars($post['author_name'] ?? '') ?>"/>
        </div>
        <div class="form-group">
          <label for="authorRole">Role / Title</label>
          <input type="text" id="authorRole" placeholder="e.g. Marketing Lead at Whatapi"
                 value="<?= htmlspecialchars($post['author_role'] ?? '') ?>"/>
        </div>
        <div class="form-group">
          <label for="authorBio">Short Bio</label>
          <textarea id="authorBio" rows="3" placeholder="Short bio shown at end of post"><?= htmlspecialchars($post['author_bio'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Thumbnail -->
      <div class="sidebar-card">
        <h3>Featured Image</h3>
        <div class="thumb-drop" id="thumbDrop" onclick="document.getElementById('thumbFile').click()">
          <div id="thumbPreview">
            <?php if (!empty($post['thumbnail_url'])): ?>
              <img src="<?= htmlspecialchars($post['thumbnail_url']) ?>" alt="Thumbnail"/>
            <?php else: ?>
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              <p>Click to upload thumbnail<br/><span style="font-size:.7rem">JPG, PNG, WebP · max 5 MB</span></p>
            <?php endif; ?>
          </div>
          <input type="file" id="thumbFile" accept="image/*" onchange="handleThumbUpload(this)"/>
        </div>
        <input type="hidden" id="thumbnailUrl" value="<?= htmlspecialchars($post['thumbnail_url'] ?? '') ?>"/>
      </div>

      <!-- Tags -->
      <div class="sidebar-card">
        <h3>Tags</h3>
        <div class="form-group">
          <input type="text" id="postTags" placeholder="WhatsApp, Marketing, India"
                 value="<?= htmlspecialchars($post['tags'] ?? '') ?>"/>
          <small>Comma-separated tags. Used for SEO keywords.</small>
        </div>
      </div>

      <!-- SEO Preview -->
      <div class="sidebar-card">
        <h3>SEO Preview</h3>
        <div class="seo-preview">
          <div class="seo-url" id="seoUrl">whatapi.in › blog › your-post-slug</div>
          <div class="seo-title" id="seoTitle">Your Post Title | Whatapi Blog</div>
          <div class="seo-desc" id="seoDesc">Your excerpt will appear here as the meta description in Google search results…</div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<script>
const API_BASE = '/backend/api';
const POST_ID  = <?= $postId ?: 'null' ?>;

/* ── Init Quill ────────────────────────────────────────────── */
const quill = new Quill('#quillEditor', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ header: [2, 3, 4, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      ['blockquote', 'code-block'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link', 'image'],
      [{ align: [] }],
      ['clean'],
    ],
  },
  placeholder: 'Start writing your article…',
});

/* ── SEO Preview ────────────────────────────────────────────── */
function updateSeoPreview() {
  const title   = document.getElementById('postTitle').value;
  const excerpt = document.getElementById('postExcerpt').value;
  const slug    = document.getElementById('postSlug').value || autoSlug(title);
  document.getElementById('seoUrl').textContent   = `whatapi.in › blog › ${slug || 'your-post-slug'}`;
  document.getElementById('seoTitle').textContent = `${title || 'Your Post Title'} | Whatapi Blog`;
  document.getElementById('seoDesc').textContent  = excerpt || 'Your excerpt will appear here as the meta description…';
}

document.getElementById('postTitle').addEventListener('input', updateSeoPreview);
document.getElementById('postExcerpt').addEventListener('input', updateSeoPreview);
document.getElementById('postSlug').addEventListener('input', updateSeoPreview);
updateSeoPreview();

/* ── Auto-slug from title ───────────────────────────────────── */
function autoSlug(title) {
  return title.toLowerCase().trim().replace(/[^a-z0-9\s-]/g,'').replace(/[\s-]+/g,'-').replace(/^-|-$/g,'');
}
document.getElementById('postTitle').addEventListener('blur', function() {
  if (!document.getElementById('postSlug').value) {
    document.getElementById('postSlug').value = autoSlug(this.value);
    updateSeoPreview();
  }
});

/* ── Thumbnail Upload ───────────────────────────────────────── */
async function handleThumbUpload(input) {
  const file = input.files[0];
  if (!file) return;

  const fd = new FormData();
  fd.append('image', file);
  showToast('Uploading image…');

  try {
    const res  = await fetch(`${API_BASE}/upload.php`, { method: 'POST', body: fd, credentials: 'include' });
    const data = await res.json();
    if (data.url) {
      document.getElementById('thumbnailUrl').value = data.url;
      const drop = document.getElementById('thumbDrop');
      drop.classList.add('has-image');
      document.getElementById('thumbPreview').innerHTML = `<img src="${data.url}" alt="Thumbnail"/>`;
      showToast('Image uploaded!', 'success');
    } else {
      showToast(data.error || 'Upload failed', 'error');
    }
  } catch {
    /* If API not set up, use local object URL as preview only */
    const url = URL.createObjectURL(file);
    document.getElementById('thumbnailUrl').value = '';
    document.getElementById('thumbDrop').classList.add('has-image');
    document.getElementById('thumbPreview').innerHTML = `<img src="${url}" alt="Thumbnail"/>`;
    showToast('Preview only — backend not connected', 'error');
  }
}

/* ── Build Payload ──────────────────────────────────────────── */
function buildPayload(status) {
  return {
    title:            document.getElementById('postTitle').value.trim(),
    slug:             document.getElementById('postSlug').value.trim(),
    excerpt:          document.getElementById('postExcerpt').value.trim(),
    content:          quill.root.innerHTML,
    category_id:      document.getElementById('postCategory').value,
    author_name:      document.getElementById('authorName').value.trim() || 'Whatapi Team',
    author_role:      document.getElementById('authorRole').value.trim(),
    author_bio:       document.getElementById('authorBio').value.trim(),
    thumbnail_url:    document.getElementById('thumbnailUrl').value,
    tags:             document.getElementById('postTags').value.trim(),
    status:           status,
  };
}

/* ── Save Draft ─────────────────────────────────────────────── */
async function saveDraft() { await savePost('draft'); }
async function publishPost() { await savePost('published'); }

async function savePost(status) {
  const payload = buildPayload(status);
  if (!payload.title) { showToast('Title is required', 'error'); return; }
  if (!payload.category_id) { showToast('Select a category', 'error'); return; }

  const method = POST_ID ? 'PUT' : 'POST';
  const url    = POST_ID
    ? `${API_BASE}/posts.php?id=${POST_ID}`
    : `${API_BASE}/posts.php`;

  try {
    const res  = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      credentials: 'include',
    });
    const data = await res.json();

    if (data.success) {
      showToast(status === 'published' ? 'Post published!' : 'Draft saved!', 'success');
      if (!POST_ID && data.id) {
        setTimeout(() => { window.location.href = `editor.php?id=${data.id}`; }, 1200);
      }
    } else {
      showToast(data.error || 'Save failed', 'error');
    }
  } catch {
    showToast('Could not connect to backend. Is the API configured?', 'error');
  }
}

/* ── Import HTML / MD file into Quill ───────────────────────── */
document.getElementById('contentFileInput').addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    const text = e.target.result;
    const isHtml = /\.(html?|htm)$/i.test(file.name);
    const html   = isHtml ? extractBodyHtml(text) : mdToHtml(text);

    if (quill.getLength() > 1) {
      if (!confirm('Replace current content with the imported file?')) return;
    }
    quill.root.innerHTML = html;
    showToast('File imported into editor', 'success');
  };
  reader.readAsText(file);
  this.value = '';
});

function extractBodyHtml(html) {
  const m = html.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
  return m ? m[1].trim() : html;
}

function mdToHtml(md) {
  return md
    .replace(/^(---+|\*\*\*+|___+)\s*$/gm, '<hr>')
    .replace(/^#{4}\s+(.+)$/gm, '<h4>$1</h4>')
    .replace(/^#{3}\s+(.+)$/gm, '<h3>$1</h3>')
    .replace(/^#{2}\s+(.+)$/gm, '<h2>$1</h2>')
    .replace(/^#{1}\s+(.+)$/gm, '<h2>$1</h2>')
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.+?)\*/g, '<em>$1</em>')
    .replace(/`(.+?)`/g, '<code>$1</code>')
    .replace(/^>\s+(.+)$/gm, '<blockquote>$1</blockquote>')
    .replace(/^[-*]\s+(.+)$/gm, '<li>$1</li>')
    .replace(/(<li>[\s\S]+?<\/li>)/g, '<ul>$1</ul>')
    .replace(/\n{2,}/g, '</p><p>')
    .replace(/^(?!<[a-z])(.+)$/gm, '<p>$1</p>')
    .replace(/<\/p><p><\/p>/g, '</p><p>');
}

/* ── Toast ──────────────────────────────────────────────────── */
function showToast(msg, type = '') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className   = 'toast show ' + type;
  setTimeout(() => { t.className = 'toast'; }, 3200);
}
</script>

</body>
</html>
