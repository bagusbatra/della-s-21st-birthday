<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';

$adminBase = '../';
$pageTitle = 'Wishes & Messages';
$activeMenu = 'messages';
include __DIR__ . '/../includes/header.php';
?>

<div class="admin-card">
  <div class="admin-empty-state">
    <h2 style="margin-top:0">🚧 Belum Tersedia</h2>
    <p>Moderasi ucapan (approve/reject/tambah manual) akan dibangun di <strong>Iterasi 5</strong>.</p>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
