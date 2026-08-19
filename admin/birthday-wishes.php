<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';

$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_valid();

    $id = (int) ($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare('DELETE FROM birthday_wishes WHERE id = ?')->execute([$id]);
        flash_set('success', 'Harapan berhasil dihapus.');
    }

    redirect('birthday-wishes');
}

$wishes = $pdo->query('SELECT * FROM birthday_wishes ORDER BY created_at DESC')->fetchAll();
$totalWishes = count($wishes);

$pageTitle = 'Harapan Ulang Tahun';
$activeMenu = 'birthday-wishes';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px">
    <h2 style="margin:0">Harapan Ulang Tahun ke-21 (<?= $totalWishes ?>)</h2>
  </div>
  <p class="admin-card--muted" style="margin-top:-8px; margin-bottom:16px">
    Setiap kali ada yang meniup 21 lilin di halaman utama dan mengisi popup "Harapan Ulang Tahun ke-21",
    harapannya tersimpan di sini. Anonim, tidak ditampilkan di situs publik — hanya kamu yang bisa membacanya.
  </p>

  <?php if (empty($wishes)): ?>
    <div class="admin-empty-state">Belum ada harapan yang masuk.</div>
  <?php else: ?>
    <div style="overflow-x:auto">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Harapan</th>
            <th>Waktu</th>
            <th style="width:100px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($wishes as $w): ?>
            <tr>
              <td style="max-width:480px"><?= nl2br(e($w['wish_text'])) ?></td>
              <td class="admin-card--muted"><?= e(format_relative_time($w['created_at'])) ?></td>
              <td>
                <form method="post" action="birthday-wishes" style="display:inline" onsubmit="return confirm('Hapus harapan ini secara permanen?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $w['id'] ?>">
                  <input type="hidden" name="action" value="delete">
                  <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Hapus</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
