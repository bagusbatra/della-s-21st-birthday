<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';

$pdo = get_pdo();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$memory = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM memories WHERE id = ?');
    $stmt->execute([$id]);
    $memory = $stmt->fetch();

    if (!$memory) {
        flash_set('error', 'Foto tidak ditemukan.');
        redirect('./');
    }
}

$errors = [];

$formValues = [
    'image_url' => $memory['image_url'] ?? '',
    'caption' => $memory['caption'] ?? '',
    'event_date' => $memory['event_date'] ?? '',
    'location' => $memory['location'] ?? '',
    'tag' => $memory['tag'] ?? '',
    'note' => $memory['note'] ?? '',
    'is_published' => $memory ? (int) $memory['is_published'] : 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_valid();

    $formValues['caption'] = trim($_POST['caption'] ?? '');
    $formValues['event_date'] = trim($_POST['event_date'] ?? '');
    $formValues['location'] = trim($_POST['location'] ?? '');
    $formValues['tag'] = trim($_POST['tag'] ?? '');
    $formValues['note'] = trim($_POST['note'] ?? '');
    $formValues['is_published'] = isset($_POST['is_published']) ? 1 : 0;
    $formValues['image_url'] = trim($_POST['image_url'] ?? '');

    if (!empty($_FILES['image_file']['name'])) {
        $uploadResult = process_image_upload($_FILES['image_file']);
        if ($uploadResult['error']) {
            $errors[] = $uploadResult['error'];
        } else {
            $formValues['image_url'] = $uploadResult['url'];
        }
    }

    if ($formValues['caption'] === '') {
        $errors[] = 'Caption wajib diisi.';
    }

    if ($formValues['image_url'] === '') {
        $errors[] = 'URL foto atau file upload wajib diisi salah satu.';
    }

    if (!$errors) {
        if ($memory) {
            // Kalau foto diganti (upload baru/URL baru), bersihkan file upload lama
            // biar tidak jadi sampah menumpuk di assets/uploads/.
            if ($memory['image_url'] && $memory['image_url'] !== $formValues['image_url']
                && str_starts_with($memory['image_url'], DELLA_UPLOAD_URL . '/')) {
                $oldPath = DELLA_UPLOAD_DIR . '/' . basename($memory['image_url']);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $stmt = $pdo->prepare(
                'UPDATE memories SET image_url=?, caption=?, event_date=?, location=?, tag=?, note=?, is_published=? WHERE id=?'
            );
            $stmt->execute([
                $formValues['image_url'], $formValues['caption'], $formValues['event_date'],
                $formValues['location'], $formValues['tag'], $formValues['note'],
                $formValues['is_published'], $memory['id'],
            ]);
            flash_set('success', 'Foto berhasil diperbarui.');
        } else {
            $nextOrder = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM memories')->fetchColumn() + 1;
            $stmt = $pdo->prepare(
                'INSERT INTO memories (image_url, caption, event_date, location, tag, note, likes, sort_order, is_published)
                 VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)'
            );
            $stmt->execute([
                $formValues['image_url'], $formValues['caption'], $formValues['event_date'],
                $formValues['location'], $formValues['tag'], $formValues['note'],
                $nextOrder, $formValues['is_published'],
            ]);
            flash_set('success', 'Foto baru berhasil ditambahkan.');
        }

        redirect('./');
    }
}

$pageTitle = $memory ? 'Edit Foto' : 'Tambah Foto Baru';
$activeMenu = 'gallery';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($errors): ?>
  <div class="admin-flash admin-flash--error">
    <?php foreach ($errors as $err): ?>
      <?= e($err) ?><br>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="admin-card">
  <h2><?= $memory ? 'Edit Foto Kenangan' : 'Tambah Foto Kenangan Baru' ?></h2>

  <form method="post" action="form<?= $memory ? '?id=' . (int) $memory['id'] : '' ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php if ($formValues['image_url']): ?>
      <div class="admin-form-group">
        <label>Preview Saat Ini</label>
        <img src="<?= e($formValues['image_url']) ?>" alt="" style="max-width:220px;border-radius:10px;border:1px solid var(--admin-border)">
      </div>
    <?php endif; ?>

    <div class="admin-form-group">
      <label for="image_url">URL Foto</label>
      <input class="admin-input" type="text" id="image_url" name="image_url" value="<?= e($formValues['image_url']) ?>" placeholder="https://...">
      <span class="hint">Bisa link Unsplash/Google Drive/dsb, ATAU upload file di bawah (upload akan menggantikan URL ini).</span>
    </div>

    <div class="admin-form-group">
      <label for="image_file">Atau Upload File (jpg/jpeg/png/webp, maks 5MB)</label>
      <input class="admin-input" type="file" id="image_file" name="image_file" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <div class="admin-form-group">
      <label for="caption">Caption</label>
      <input class="admin-input" type="text" id="caption" name="caption" value="<?= e($formValues['caption']) ?>" required>
    </div>

    <div class="admin-form-group">
      <label for="event_date">Tanggal</label>
      <input class="admin-input" type="text" id="event_date" name="event_date" value="<?= e($formValues['event_date']) ?>" placeholder="Contoh: 14 Februari">
    </div>

    <div class="admin-form-group">
      <label for="location">Lokasi</label>
      <input class="admin-input" type="text" id="location" name="location" value="<?= e($formValues['location']) ?>" placeholder="Contoh: Café Kenangan, Sudirman">
    </div>

    <div class="admin-form-group">
      <label for="tag">Tag Kategori</label>
      <input class="admin-input" type="text" id="tag" name="tag" value="<?= e($formValues['tag']) ?>" placeholder="Contoh: Momen Manis">
    </div>

    <div class="admin-form-group">
      <label for="note">Catatan Kenangan</label>
      <textarea class="admin-textarea" id="note" name="note"><?= e($formValues['note']) ?></textarea>
    </div>

    <div class="admin-form-group">
      <label>
        <input type="checkbox" name="is_published" value="1" <?= $formValues['is_published'] ? 'checked' : '' ?>>
        Tayangkan di situs publik
      </label>
    </div>

    <button type="submit" class="admin-btn"><?= $memory ? 'Simpan Perubahan' : 'Tambah Foto' ?></button>
    <a href="./" class="admin-btn admin-btn--secondary">Batal</a>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
