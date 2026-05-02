<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin Login – Whatapi</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <meta name="robots" content="noindex, nofollow"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0B1613 0%, #0e2218 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .login-card {
      background: #fff;
      border-radius: 16px;
      padding: 44px 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 24px 60px rgba(0,0,0,.3);
    }
    .login-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 32px;
    }
    .login-logo-icon {
      width: 40px; height: 40px;
      background: #25D366;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
    }
    .login-logo span { font-size: 1.2rem; font-weight: 600; color: #0F172A; }
    .login-card h1 { font-size: 1.35rem; font-weight: 600; margin-bottom: 6px; color: #0F172A; }
    .login-card p  { font-size: .86rem; color: #64748B; margin-bottom: 28px; }
    .form-group { margin-bottom: 18px; }
    label { display: block; font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: 7px; }
    input[type=email], input[type=password] {
      width: 100%;
      padding: 12px 14px;
      font-family: inherit;
      font-size: .9rem;
      border: 1.5px solid #E2E8F0;
      border-radius: 8px;
      color: #0F172A;
      outline: none;
      transition: border-color .2s;
    }
    input:focus { border-color: #25D366; box-shadow: 0 0 0 3px rgba(37,211,102,.12); }
    .btn-login {
      width: 100%;
      padding: 13px;
      background: #25D366;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-family: inherit;
      font-size: .95rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 6px;
      transition: background .2s;
    }
    .btn-login:hover { background: #1DA851; }
    .error {
      background: #FEF2F2;
      border: 1px solid #FCA5A5;
      color: #991B1B;
      padding: 10px 14px;
      border-radius: 8px;
      font-size: .84rem;
      margin-bottom: 18px;
    }
    .back-link { display: block; text-align: center; margin-top: 20px; font-size: .82rem; color: #64748B; }
    .back-link a { color: #128C7E; }
  </style>
</head>
<body>
<?php
require_once __DIR__ . '/../includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (attemptLogin($email, $pass)) {
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid email or password.';
}

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>

<div class="login-card">
  <div class="login-logo">
    <div class="login-logo-icon">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
    </div>
    <span>Whatapi</span>
  </div>

  <h1>Admin Login</h1>
  <p>Blog management panel</p>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="on">
    <div class="form-group">
      <label for="email">Email address</label>
      <input type="email" id="email" name="email" required autofocus
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
             placeholder="admin@yourdomain.com"/>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required placeholder="••••••••"/>
    </div>
    <button type="submit" class="btn-login">Sign In →</button>
  </form>

  <span class="back-link"><a href="/">← Back to website</a></span>
</div>

</body>
</html>
