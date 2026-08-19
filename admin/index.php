<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/config.php';

$pdo = get_pdo();

$memoriesCount = (int) $pdo->query('SELECT COUNT(*) FROM memories')->fetchColumn();
$messagesPendingCount = (int) $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'pending'")->fetchColumn();
$messagesApprovedCount = (int) $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'approved'")->fetchColumn();
$birthdayWishesCount = (int) $pdo->query('SELECT COUNT(*) FROM birthday_wishes')->fetchColumn();
$isReleasedNow = time() >= DELLA_RELEASE_TIMESTAMP;
$daysUntilRelease = max(0, (int) ceil((DELLA_RELEASE_TIMESTAMP - time()) / 86400));

$pageTitle = 'Dashboard';
$activeMenu = 'dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-grid">
  <div class="admin-stat">
    <div class="admin-stat__value"><?= $memoriesCount ?></div>
    <div class="admin-stat__label">Foto di Gallery</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat__value"><?= $messagesApprovedCount ?></div>
    <div class="admin-stat__label">Ucapan Tayang (Approved)</div>
  </div>
  <a href="messages/?status=pending" class="admin-stat" style="display:block; text-decoration:none; color:inherit">
    <div class="admin-stat__value" style="<?= $messagesPendingCount > 0 ? 'color:var(--admin-warning)' : '' ?>"><?= $messagesPendingCount ?></div>
    <div class="admin-stat__label">Ucapan Menunggu Moderasi <?= $messagesPendingCount > 0 ? '→' : '' ?></div>
  </a>
  <a href="birthday-wishes" class="admin-stat" style="display:block; text-decoration:none; color:inherit">
    <div class="admin-stat__value"><?= $birthdayWishesCount ?></div>
    <div class="admin-stat__label">Harapan Ulang Tahun (Tiup Lilin)</div>
  </a>
  <div class="admin-stat">
    <div class="admin-stat__value"><?= $isReleasedNow ? icon('sparkles', 'icon icon-lg') : $daysUntilRelease ?></div>
    <div class="admin-stat__label">
      <?= $isReleasedNow ? 'Situs Sudah Rilis!' : 'Hari Menuju Rilis (' . e(format_indonesian_datetime(DELLA_RELEASE_TIMESTAMP)) . ')' ?>
    </div>
  </div>
</div>

<div class="admin-card" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px">
  <div>
    <h2 style="margin:0 0 4px">Preview Situs</h2>
    <p class="admin-card--muted" style="margin:0">
      <?= $isReleasedNow
        ? 'Situs sudah rilis ke publik — tautan ini sama dengan yang dilihat semua orang.'
        : 'Situs masih terkunci untuk publik sampai tanggal rilis. Karena kamu login sebagai admin, kamu tetap bisa melihat tampilan aslinya lebih awal di sini.' ?>
    </p>
  </div>
  <a href="<?= e($siteBaseUrl) ?>" target="_blank" rel="noopener" class="admin-btn">
    <?= icon('eye', 'icon icon-sm') ?> Buka Preview
  </a>
</div>

<div class="admin-card">
  <h2>Selamat datang, <?= e(current_admin()['display_name'] ?: current_admin()['username']) ?></h2>
  <p class="admin-card--muted">
    Semua konten utama <code>index.php</code> — Hero, Gate &amp; Countdown, Cake, Gallery,
    Love Letter, dan Wishes/Messages — sekarang dikelola dari sini dan tersimpan di
    database <code>della_birthday</code>. Detail iterasi pengembangan ada di
    <code>RENCANA-PENGEMBANGAN-ADMIN.md</code>.
  </p>
  <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:14px">
    <a href="hero" class="admin-btn admin-btn--secondary admin-btn--sm">Edit Hero</a>
    <a href="gate-settings" class="admin-btn admin-btn--secondary admin-btn--sm">Edit Gate & Countdown</a>
    <a href="cake" class="admin-btn admin-btn--secondary admin-btn--sm">Edit Cake Section</a>
    <a href="gallery/" class="admin-btn admin-btn--secondary admin-btn--sm">Kelola Gallery</a>
    <a href="letter" class="admin-btn admin-btn--secondary admin-btn--sm">Edit Love Letter</a>
    <a href="messages/" class="admin-btn admin-btn--secondary admin-btn--sm">Moderasi Pesan</a>
    <a href="birthday-wishes" class="admin-btn admin-btn--secondary admin-btn--sm">Lihat Harapan Ulang Tahun</a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
