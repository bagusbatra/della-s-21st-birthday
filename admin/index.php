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
  <div class="admin-stat">
    <div class="admin-stat__value"><?= $messagesPendingCount ?></div>
    <div class="admin-stat__label">Ucapan Menunggu Moderasi</div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat__value"><?= $daysUntilRelease ?></div>
    <div class="admin-stat__label">Hari Menuju Rilis (19 Agustus 2026)</div>
  </div>
</div>

<div class="admin-card">
  <h2>Selamat datang 👋</h2>
  <p class="admin-card--muted">
    Ini adalah fondasi admin panel (Iterasi 0). Menu di sidebar sudah mencerminkan
    setiap section pada <code>index.php</code>, tapi sebagian besar halamannya masih
    berupa placeholder "Coming Soon" — CRUD sungguhan untuk masing-masing section
    akan dibangun bertahap di iterasi berikutnya sesuai
    <code>RENCANA-PENGEMBANGAN-ADMIN.md</code>.
  </p>
  <p class="admin-card--muted">
    Data yang kamu lihat di kartu statistik atas ini <strong>sudah nyata</strong> dari
    database <code>della_birthday</code> — bukan dummy — karena tabel <code>memories</code>
    dan <code>messages</code> sudah diisi hasil migrasi dari <code>data.js</code>.
  </p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
