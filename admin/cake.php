<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/settings.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_valid();

    settings_set_many([
        'cake_banner_name' => trim($_POST['cake_banner_name'] ?? ''),
        'cake_banner_tagline' => trim($_POST['cake_banner_tagline'] ?? ''),
        'cake_banner_date' => trim($_POST['cake_banner_date'] ?? ''),
        'cake_banner_recipient' => trim($_POST['cake_banner_recipient'] ?? ''),
    ]);

    flash_set('success', 'Teks Cake Section berhasil diperbarui.');
    redirect('cake');
}

$cakeBannerName = settings_get('cake_banner_name');
$cakeBannerTagline = settings_get('cake_banner_tagline');
$cakeBannerDate = settings_get('cake_banner_date');
$cakeBannerRecipient = settings_get('cake_banner_recipient');

$pageTitle = 'Cake Section';
$activeMenu = 'cake';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
  <h2>Edit Teks Ilustrasi Kue</h2>
  <p class="admin-card--muted">
    Ini teks pada ilustrasi kue 3 lapis di section "Tiup 21 Lilin". 21 lilin interaktif &amp; tombol
    "Make a Wish" tidak dikelola di sini karena murni interaksi client-side, tidak ada datanya di database.
  </p>

  <form method="post" action="cake">
    <?= csrf_field() ?>

    <div class="admin-form-group">
      <label for="cake_banner_name">Lapisan 1 — Nama</label>
      <input class="admin-input" type="text" id="cake_banner_name" name="cake_banner_name" value="<?= e($cakeBannerName) ?>" required>
      <span class="hint">Contoh: Della 21st</span>
    </div>

    <div class="admin-form-group">
      <label for="cake_banner_tagline">Lapisan 2 — Tagline</label>
      <input class="admin-input" type="text" id="cake_banner_tagline" name="cake_banner_tagline" value="<?= e($cakeBannerTagline) ?>" required>
      <span class="hint">Contoh: Happy Birthday My Love</span>
    </div>

    <div class="admin-form-group">
      <label for="cake_banner_date">Lapisan 3 — Kiri (tanggal)</label>
      <input class="admin-input" type="text" id="cake_banner_date" name="cake_banner_date" value="<?= e($cakeBannerDate) ?>" required>
      <span class="hint">Contoh: 19 Agustus</span>
    </div>

    <div class="admin-form-group">
      <label for="cake_banner_recipient">Lapisan 3 — Kanan (nama penerima)</label>
      <input class="admin-input" type="text" id="cake_banner_recipient" name="cake_banner_recipient" value="<?= e($cakeBannerRecipient) ?>" required>
      <span class="hint">Contoh: Della Puspa Ardiati</span>
    </div>

    <button type="submit" class="admin-btn">Simpan Perubahan</button>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
