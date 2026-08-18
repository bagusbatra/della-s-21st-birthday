<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../config/db.php';

$memories = get_pdo()->query('SELECT * FROM memories ORDER BY sort_order ASC, id ASC')->fetchAll();

$pageTitle = 'Gallery / Memories';
$activeMenu = 'gallery';
include __DIR__ . '/../includes/header.php';
?>

<div class="admin-card">
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px">
    <h2 style="margin:0">Foto Kenangan (<?= count($memories) ?>)</h2>
    <a href="form" class="admin-btn">+ Tambah Foto Baru</a>
  </div>

  <?php if (empty($memories)): ?>
    <div class="admin-empty-state">Belum ada foto. Klik "+ Tambah Foto Baru" untuk mulai.</div>
  <?php else: ?>
    <div style="overflow-x:auto">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width:64px">Foto</th>
            <th>Caption</th>
            <th>Tanggal / Tag</th>
            <th>Status</th>
            <th>Urutan</th>
            <th style="width:220px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($memories as $i => $m): ?>
            <tr>
              <td>
                <img src="<?= e($m['image_url']) ?>" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:8px;border:1px solid var(--admin-border)">
              </td>
              <td>
                <strong><?= e($m['caption']) ?></strong>
                <div class="admin-card--muted" style="margin-top:2px"><?= e($m['location']) ?></div>
              </td>
              <td>
                <?= e($m['event_date']) ?><br>
                <span class="admin-badge admin-badge--muted"><?= e($m['tag'] ?: '—') ?></span>
              </td>
              <td>
                <?php if ((int) $m['is_published'] === 1): ?>
                  <span class="admin-badge admin-badge--success">Tayang</span>
                <?php else: ?>
                  <span class="admin-badge admin-badge--muted">Disembunyikan</span>
                <?php endif; ?>
              </td>
              <td>
                <form method="post" action="actions" style="display:inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                  <input type="hidden" name="action" value="move_up">
                  <button type="submit" class="admin-btn admin-btn--secondary admin-btn--sm" <?= $i === 0 ? 'disabled' : '' ?>>↑</button>
                </form>
                <form method="post" action="actions" style="display:inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                  <input type="hidden" name="action" value="move_down">
                  <button type="submit" class="admin-btn admin-btn--secondary admin-btn--sm" <?= $i === count($memories) - 1 ? 'disabled' : '' ?>>↓</button>
                </form>
              </td>
              <td>
                <a href="form?id=<?= (int) $m['id'] ?>" class="admin-btn admin-btn--secondary admin-btn--sm">Edit</a>

                <form method="post" action="actions" style="display:inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                  <input type="hidden" name="action" value="toggle_publish">
                  <button type="submit" class="admin-btn admin-btn--secondary admin-btn--sm">
                    <?= (int) $m['is_published'] === 1 ? 'Sembunyikan' : 'Tayangkan' ?>
                  </button>
                </form>

                <form method="post" action="actions" style="display:inline" onsubmit="return confirm('Hapus foto ini? Tindakan ini tidak bisa dibatalkan.');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
