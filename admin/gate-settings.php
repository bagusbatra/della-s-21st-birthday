<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/settings.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_valid();

    $rawInput = trim($_POST['release_datetime'] ?? '');
    $timestamp = $rawInput !== '' ? strtotime($rawInput) : false;

    if ($timestamp === false) {
        $error = 'Format tanggal/jam tidak valid. Silakan pilih ulang lewat kalender.';
    } else {
        settings_set('release_timestamp', date('Y-m-d H:i:s', $timestamp));
        flash_set('success', 'Tanggal rilis berhasil diperbarui.');
        redirect('gate-settings');
    }
}

// Nilai untuk input datetime-local butuh format "Y-m-d\TH:i".
$currentReleaseTimestamp = DELLA_RELEASE_TIMESTAMP;
$currentReleaseForInput = date('Y-m-d\TH:i', $currentReleaseTimestamp);
$isReleasedNow = time() >= $currentReleaseTimestamp;

$pageTitle = 'Gate & Countdown';
$activeMenu = 'gate';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?>
  <div class="admin-flash admin-flash--error"><?= e($error) ?></div>
<?php endif; ?>

<div class="admin-card">
  <h2>Tanggal & Jam Rilis</h2>
  <p class="admin-card--muted">
    Sebelum tanggal ini, pengunjung publik hanya melihat halaman gerbang terkunci
    (dengan countdown). Setelah tanggal ini terlewati, situs penuh otomatis terbuka
    untuk semua orang — tidak perlu ubah kode apa pun.
  </p>

  <p style="margin-bottom:16px">
    Status saat ini:
    <?php if ($isReleasedNow): ?>
      <span class="admin-badge admin-badge--success">Sudah Rilis</span>
    <?php else: ?>
      <span class="admin-badge admin-badge--warning">Masih Terkunci</span>
    <?php endif; ?>
  </p>

  <form method="post" action="gate-settings">
    <?= csrf_field() ?>

    <div class="admin-form-group">
      <label for="release_datetime">Tanggal &amp; Jam Rilis (WIB)</label>
      <input class="admin-input" type="datetime-local" id="release_datetime" name="release_datetime" value="<?= e($currentReleaseForInput) ?>" required>
    </div>

    <button type="submit" class="admin-btn">Simpan Tanggal Rilis</button>
  </form>
</div>

<div class="admin-card">
  <h2>Mode Developer</h2>
  <p class="admin-card--muted">
    Mode developer masih dikendalikan manual lewat URL, belum ada toggle di sini (lihat
    <code>RENCANA-PENGEMBANGAN-ADMIN.md</code> — poin keputusan Iterasi 2 kalau ini mau diubah nanti).
  </p>
  <ul class="admin-card--muted" style="margin:0; padding-left:18px">
    <li><code>?dev=on</code> (di situs publik) — buka situs penuh lebih awal untuk keperluan development.</li>
    <li><code>?dev=off</code> (di situs publik) — matikan lagi mode developer sebelum situs dirilis ke publik.</li>
  </ul>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
