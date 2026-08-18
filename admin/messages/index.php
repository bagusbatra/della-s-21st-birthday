<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../config/db.php';

$pdo = get_pdo();

$statusFilter = $_GET['status'] ?? 'all';
$allowedFilters = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($statusFilter, $allowedFilters, true)) {
    $statusFilter = 'all';
}

if ($statusFilter === 'all') {
    $messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
} else {
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE status = ? ORDER BY created_at DESC');
    $stmt->execute([$statusFilter]);
    $messages = $stmt->fetchAll();
}

$counts = $pdo->query(
    "SELECT status, COUNT(*) c FROM messages GROUP BY status"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$pageTitle = 'Wishes & Messages';
$activeMenu = 'messages';
include __DIR__ . '/../includes/header.php';

$statusBadgeClass = [
    'pending' => 'admin-badge--warning',
    'approved' => 'admin-badge--success',
    'rejected' => 'admin-badge--danger',
];
$statusLabel = [
    'pending' => 'Menunggu',
    'approved' => 'Tayang',
    'rejected' => 'Ditolak',
];
?>

<div class="admin-card">
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px">
    <h2 style="margin:0">
      Ucapan & Pesan
      (<?= count($messages) ?><?= $statusFilter !== 'all' ? ' ' . e($statusLabel[$statusFilter]) : '' ?>)
    </h2>
    <a href="form" class="admin-btn">+ Tambah Manual</a>
  </div>

  <div style="display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap">
    <?php foreach ($allowedFilters as $f): ?>
      <?php
        $label = $f === 'all' ? 'Semua' : $statusLabel[$f];
        $count = $f === 'all'
            ? array_sum($counts)
            : ($counts[$f] ?? 0);
      ?>
      <a
        href="?status=<?= e($f) ?>"
        class="admin-btn admin-btn--sm <?= $statusFilter === $f ? '' : 'admin-btn--secondary' ?>"
      ><?= e($label) ?> (<?= $count ?>)</a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($messages)): ?>
    <div class="admin-empty-state">Tidak ada pesan untuk filter ini.</div>
  <?php else: ?>
    <div style="overflow-x:auto">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Pengirim</th>
            <th>Pesan</th>
            <th>Status</th>
            <th>Sumber</th>
            <th>Waktu</th>
            <th style="width:260px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($messages as $m): ?>
            <tr>
              <td>
                <strong><?= $m['is_anonymous'] ? 'Anonim' : e($m['sender_name']) ?></strong>
                <div class="admin-card--muted"><?= e($m['role_relation']) ?></div>
              </td>
              <td style="max-width:320px">
                <?= e(mb_strimwidth($m['message'], 0, 120, '…')) ?>
                <?php if (!empty($m['photo_url'])): ?>
                  <div style="margin-top:6px">
                    <img src="<?= e($m['photo_url']) ?>" alt="" style="max-width:80px;max-height:80px;border-radius:8px;border:1px solid var(--admin-border);object-fit:cover">
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <span class="admin-badge <?= $statusBadgeClass[$m['status']] ?? 'admin-badge--muted' ?>">
                  <?= e($statusLabel[$m['status']] ?? $m['status']) ?>
                </span>
              </td>
              <td><span class="admin-badge admin-badge--muted"><?= e($m['source']) ?></span></td>
              <td class="admin-card--muted"><?= e(format_relative_time($m['created_at'])) ?></td>
              <td>
                <a href="form?id=<?= (int) $m['id'] ?>" class="admin-btn admin-btn--secondary admin-btn--sm">Edit</a>

                <?php if ($m['status'] !== 'approved'): ?>
                  <form method="post" action="actions" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="admin-btn admin-btn--sm" style="background:var(--admin-success)">Approve</button>
                  </form>
                <?php endif; ?>

                <?php if ($m['status'] !== 'rejected'): ?>
                  <form method="post" action="actions" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="admin-btn admin-btn--secondary admin-btn--sm">Reject</button>
                  </form>
                <?php endif; ?>

                <form method="post" action="actions" style="display:inline" onsubmit="return confirm('Hapus pesan ini secara permanen?');">
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
