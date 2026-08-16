<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';

$pdo = get_pdo();
$adminId = (int) current_admin()['id'];

$stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

$profileErrors = [];
$passwordErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'profile') {
    csrf_require_valid();

    $displayName = trim($_POST['display_name'] ?? '');
    $username = trim($_POST['username'] ?? '');

    if ($username === '') {
        $profileErrors[] = 'Username tidak boleh kosong.';
    }

    if (!$profileErrors) {
        $check = $pdo->prepare('SELECT id FROM admins WHERE username = ? AND id != ?');
        $check->execute([$username, $adminId]);
        if ($check->fetch()) {
            $profileErrors[] = 'Username sudah dipakai akun lain.';
        }
    }

    if (!$profileErrors) {
        $pdo->prepare('UPDATE admins SET display_name = ?, username = ? WHERE id = ?')
            ->execute([$displayName, $username, $adminId]);

        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_display_name'] = $displayName;

        flash_set('success', 'Profil berhasil diperbarui.');
        redirect('settings.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'password') {
    csrf_require_valid();

    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if (!password_verify($currentPassword, $admin['password_hash'])) {
        $passwordErrors[] = 'Password saat ini salah.';
    }
    if (strlen($newPassword) < 8) {
        $passwordErrors[] = 'Password baru minimal 8 karakter.';
    }
    if ($newPassword !== $confirmPassword) {
        $passwordErrors[] = 'Konfirmasi password baru tidak cocok.';
    }

    if (!$passwordErrors) {
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([$newHash, $adminId]);
        flash_set('success', 'Password berhasil diganti.');
        redirect('settings.php');
    }
}

$pageTitle = 'Settings Admin';
$activeMenu = 'settings';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
  <h2>Profil Admin</h2>

  <?php if ($profileErrors): ?>
    <div class="admin-flash admin-flash--error">
      <?php foreach ($profileErrors as $err): ?><?= e($err) ?><br><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="settings.php">
    <?= csrf_field() ?>
    <input type="hidden" name="form" value="profile">

    <div class="admin-form-group">
      <label for="display_name">Nama Tampilan</label>
      <input class="admin-input" type="text" id="display_name" name="display_name" value="<?= e($admin['display_name']) ?>">
    </div>

    <div class="admin-form-group">
      <label for="username">Username</label>
      <input class="admin-input" type="text" id="username" name="username" value="<?= e($admin['username']) ?>" required>
    </div>

    <button type="submit" class="admin-btn">Simpan Profil</button>
  </form>
</div>

<div class="admin-card">
  <h2>Ganti Password</h2>

  <?php if ($passwordErrors): ?>
    <div class="admin-flash admin-flash--error">
      <?php foreach ($passwordErrors as $err): ?><?= e($err) ?><br><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="settings.php">
    <?= csrf_field() ?>
    <input type="hidden" name="form" value="password">

    <div class="admin-form-group">
      <label for="current_password">Password Saat Ini</label>
      <input class="admin-input" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
    </div>

    <div class="admin-form-group">
      <label for="new_password">Password Baru (min. 8 karakter)</label>
      <input class="admin-input" type="password" id="new_password" name="new_password" autocomplete="new-password" required minlength="8">
    </div>

    <div class="admin-form-group">
      <label for="confirm_password">Konfirmasi Password Baru</label>
      <input class="admin-input" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required minlength="8">
    </div>

    <button type="submit" class="admin-btn">Ganti Password</button>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
