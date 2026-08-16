<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/settings.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_valid();

    settings_set_many([
        'hero_badge_text' => trim($_POST['hero_badge_text'] ?? ''),
        'hero_title_line1' => trim($_POST['hero_title_line1'] ?? ''),
        'hero_title_line2' => trim($_POST['hero_title_line2'] ?? ''),
        'hero_quote' => trim($_POST['hero_quote'] ?? ''),
    ]);

    flash_set('success', 'Hero Section berhasil diperbarui.');
    redirect('hero.php');
}

$heroBadgeText = settings_get('hero_badge_text');
$heroTitleLine1 = settings_get('hero_title_line1');
$heroTitleLine2 = settings_get('hero_title_line2');
$heroQuote = settings_get('hero_quote');

$pageTitle = 'Hero Section';
$activeMenu = 'hero';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
  <h2>Edit Hero Section</h2>
  <p class="admin-card--muted">Konten ini tampil paling atas di <code>index.php</code>, tepat di bawah navbar.</p>

  <form method="post" action="hero.php">
    <?= csrf_field() ?>

    <div class="admin-form-group">
      <label for="hero_badge_text">Teks Badge (di atas judul)</label>
      <input class="admin-input" type="text" id="hero_badge_text" name="hero_badge_text" value="<?= e($heroBadgeText) ?>" required>
      <span class="hint">Contoh: ✨ 19 Agustus • 21st Special Milestone ✨</span>
    </div>

    <div class="admin-form-group">
      <label for="hero_title_line1">Judul — Baris 1</label>
      <input class="admin-input" type="text" id="hero_title_line1" name="hero_title_line1" value="<?= e($heroTitleLine1) ?>" required>
      <span class="hint">Contoh: Selamat Ulang Tahun ke-21,</span>
    </div>

    <div class="admin-form-group">
      <label for="hero_title_line2">Judul — Baris 2 (nama, tampil besar bergaya script)</label>
      <input class="admin-input" type="text" id="hero_title_line2" name="hero_title_line2" value="<?= e($heroTitleLine2) ?>" required>
      <span class="hint">Contoh: Della Puspa Ardiati</span>
    </div>

    <div class="admin-form-group">
      <label for="hero_quote">Kutipan / Quote</label>
      <textarea class="admin-textarea" id="hero_quote" name="hero_quote" required><?= e($heroQuote) ?></textarea>
      <span class="hint">Tampil sebagai kalimat italic di bawah judul, otomatis diberi tanda kutip.</span>
    </div>

    <button type="submit" class="admin-btn">Simpan Perubahan</button>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
