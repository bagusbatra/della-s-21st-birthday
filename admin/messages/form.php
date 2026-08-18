<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';

$pdo = get_pdo();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$message = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
    $stmt->execute([$id]);
    $message = $stmt->fetch();

    if (!$message) {
        flash_set('error', 'Pesan tidak ditemukan.');
        redirect('./');
    }
}

$errors = [];

$formValues = [
    'sender_name' => $message['sender_name'] ?? '',
    'is_anonymous' => $message ? (int) $message['is_anonymous'] : 0,
    'role_relation' => $message['role_relation'] ?? '',
    'avatar_emoji' => $message['avatar_emoji'] ?? '🌸',
    'hint' => $message['hint'] ?? '',
    'message' => $message['message'] ?? '',
    'photo_url' => $message['photo_url'] ?? '',
    'status' => $message['status'] ?? 'approved',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_valid();

    $formValues['sender_name'] = trim($_POST['sender_name'] ?? '');
    $formValues['is_anonymous'] = isset($_POST['is_anonymous']) ? 1 : 0;
    $formValues['role_relation'] = trim($_POST['role_relation'] ?? '');
    $formValues['avatar_emoji'] = trim($_POST['avatar_emoji'] ?? '') ?: '🌸';
    $formValues['hint'] = trim($_POST['hint'] ?? '');
    $formValues['message'] = trim($_POST['message'] ?? '');
    $formValues['status'] = in_array($_POST['status'] ?? '', ['pending', 'approved', 'rejected'], true)
        ? $_POST['status']
        : 'approved';

    if (!empty($_POST['remove_photo'])) {
        $formValues['photo_url'] = '';
    }
    if (!empty($_FILES['photo']['name'])) {
        $uploadResult = process_image_upload($_FILES['photo']);
        if ($uploadResult['error']) {
            $errors[] = $uploadResult['error'];
        } else {
            $formValues['photo_url'] = $uploadResult['url'];
        }
    }

    if ($formValues['message'] === '') {
        $errors[] = 'Isi pesan wajib diisi.';
    }
    if (!$formValues['is_anonymous'] && $formValues['sender_name'] === '') {
        $errors[] = 'Nama pengirim wajib diisi (atau centang "Kirim sebagai anonim").';
    }

    if (!$errors) {
        if ($message) {
            // Kalau foto diganti/dihapus, bersihkan file lama biar tidak jadi sampah di disk.
            if ($message['photo_url'] && $message['photo_url'] !== $formValues['photo_url']
                && str_starts_with($message['photo_url'], DELLA_UPLOAD_URL . '/')) {
                $oldPath = DELLA_UPLOAD_DIR . '/' . basename($message['photo_url']);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $stmt = $pdo->prepare(
                'UPDATE messages SET sender_name=?, is_anonymous=?, role_relation=?, avatar_emoji=?, hint=?, message=?, photo_url=?, status=? WHERE id=?'
            );
            $stmt->execute([
                $formValues['sender_name'], $formValues['is_anonymous'], $formValues['role_relation'],
                $formValues['avatar_emoji'], $formValues['hint'], $formValues['message'],
                $formValues['photo_url'] ?: null, $formValues['status'], $message['id'],
            ]);
            flash_set('success', 'Pesan berhasil diperbarui.');
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO messages (sender_name, is_anonymous, role_relation, avatar_emoji, hint, message, photo_url, status, source, likes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'admin', 0)"
            );
            $stmt->execute([
                $formValues['sender_name'], $formValues['is_anonymous'], $formValues['role_relation'],
                $formValues['avatar_emoji'], $formValues['hint'], $formValues['message'],
                $formValues['photo_url'] ?: null, $formValues['status'],
            ]);
            flash_set('success', 'Pesan baru berhasil ditambahkan.');
        }

        redirect('./');
    }
}

$pageTitle = $message ? 'Edit Pesan' : 'Tambah Pesan Manual';
$activeMenu = 'messages';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($errors): ?>
  <div class="admin-flash admin-flash--error">
    <?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="admin-card">
  <h2><?= $message ? 'Edit Pesan' : 'Tambah Pesan Manual' ?></h2>
  <p class="admin-card--muted">
    Gunakan ini untuk memasukkan ucapan yang kamu terima lewat WhatsApp/chat lain secara manual.
  </p>

  <form method="post" action="form<?= $message ? '?id=' . (int) $message['id'] : '' ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="admin-form-group">
      <label>
        <input type="checkbox" name="is_anonymous" value="1" <?= $formValues['is_anonymous'] ? 'checked' : '' ?>>
        Kirim sebagai anonim (nama disembunyikan di situs publik)
      </label>
    </div>

    <div class="admin-form-group">
      <label for="sender_name">Nama Pengirim</label>
      <input class="admin-input" type="text" id="sender_name" name="sender_name" value="<?= e($formValues['sender_name']) ?>" placeholder="Contoh: Maya & Salsa">
    </div>

    <div class="admin-form-group">
      <label for="role_relation">Hubungan / Peran</label>
      <input class="admin-input" type="text" id="role_relation" name="role_relation" value="<?= e($formValues['role_relation']) ?>" placeholder="Contoh: Bestie Kampus">
    </div>

    <div class="admin-form-group">
      <label for="avatar_emoji">Emoji Amplop</label>
      <input class="admin-input" type="text" id="avatar_emoji" name="avatar_emoji" value="<?= e($formValues['avatar_emoji']) ?>" style="max-width:100px">
    </div>

    <div class="admin-form-group">
      <label for="hint">Petunjuk Rahasia (Hint)</label>
      <input class="admin-input" type="text" id="hint" name="hint" value="<?= e($formValues['hint']) ?>" placeholder="Contoh: Pesan dari sahabat satu gengmu">
    </div>

    <div class="admin-form-group">
      <label for="message">Isi Pesan</label>
      <textarea class="admin-textarea" id="message" name="message" rows="4" required><?= e($formValues['message']) ?></textarea>
    </div>

    <div class="admin-form-group">
      <label>Foto (Opsional)</label>
      <?php if ($formValues['photo_url']): ?>
        <div style="margin-bottom:8px">
          <img src="<?= e($formValues['photo_url']) ?>" alt="" style="max-width:220px;border-radius:10px;border:1px solid var(--admin-border);display:block;margin-bottom:6px">
          <label style="font-weight:400">
            <input type="checkbox" name="remove_photo" value="1">
            Hapus foto ini
          </label>
        </div>
      <?php endif; ?>
      <input class="admin-input" type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp">
      <span class="hint">jpg/png/webp, maks 5MB. Upload file baru akan menggantikan foto yang ada.</span>
    </div>

    <div class="admin-form-group">
      <label for="status">Status</label>
      <select class="admin-select" id="status" name="status">
        <option value="approved" <?= $formValues['status'] === 'approved' ? 'selected' : '' ?>>Approved (langsung tayang)</option>
        <option value="pending" <?= $formValues['status'] === 'pending' ? 'selected' : '' ?>>Pending (menunggu review)</option>
        <option value="rejected" <?= $formValues['status'] === 'rejected' ? 'selected' : '' ?>>Rejected (ditolak)</option>
      </select>
    </div>

    <button type="submit" class="admin-btn"><?= $message ? 'Simpan Perubahan' : 'Tambah Pesan' ?></button>
    <a href="./" class="admin-btn admin-btn--secondary">Batal</a>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
