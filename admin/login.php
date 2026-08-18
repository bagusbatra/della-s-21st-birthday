<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/icons.php';

if (is_admin_logged_in()) {
    redirect('./');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_valid();

    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = get_pdo()->prepare('SELECT id, username, password_hash, display_name FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            admin_login($admin);

            $redirectTo = $_GET['redirect'] ?? './';
            // Cegah open redirect: hanya izinkan path relatif di dalam /admin.
            if (!is_string($redirectTo) || $redirectTo === '' || str_starts_with($redirectTo, '//') || str_contains($redirectTo, '://')) {
                $redirectTo = './';
            }

            redirect($redirectTo);
        }

        $error = 'Username atau password salah.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin · Della's 21st Birthday</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-login-page">
    <div class="admin-login-card">
      <h1><?= icon('lock', 'icon') ?> Admin Panel</h1>
      <p class="sub">Della's 21st Birthday</p>

      <?php if ($error): ?>
        <div class="admin-flash admin-flash--error"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="login<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>">
        <?= csrf_field() ?>
        <div class="admin-form-group">
          <label for="username">Username</label>
          <input class="admin-input" type="text" id="username" name="username" autocomplete="username" required autofocus>
        </div>
        <div class="admin-form-group">
          <label for="password">Password</label>
          <input class="admin-input" type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="admin-btn">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>
