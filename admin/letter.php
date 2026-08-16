<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/helpers.php';

$pageTitle = 'Love Letter';
$activeMenu = 'letter';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
  <div class="admin-empty-state">
    <h2 style="margin-top:0">🚧 Belum Tersedia</h2>
    <p>Form untuk mengelola isi surat cinta akan dibangun di <strong>Iterasi 4</strong>.</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
