<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../config/db.php';

$pdo = get_pdo();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$message = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
    $stmt->execute([$id]);
    $message = $stmt->fetch();

    if (!$message) {
        flash_set('error', 'Pesan tidak ditemukan.');
        redirect('index.php');
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

    if ($formValues['message'] === '') {
        $errors[] = 'Isi pesan wajib diisi.';
    }
    if (!$formValues['is_anonymous'] && $formValues['sender_name'] === '') {
        $errors[] = 'Nama pengirim wajib diisi (atau centang "Kirim sebagai anonim").';
    }

    if (!$errors) {
        if ($message) {
            $stmt = $pdo->prepare(
                'UPDATE messages SET sender_name=?, is_anonymous=?, role_relation=?, avatar_emoji=?, hint=?, message=?, status=? WHERE id=?'
            );
            $stmt->execute([
                $formValues['sender_name'], $formValues['is_anonymous'], $formValues['role_relation'],
                $formValues['avatar_emoji'], $formValues['hint'], $formValues['message'],
                $formValues['status'], $message['id'],
            ]);
            flash_set('success', 'Pesan berhasil diperbarui.');
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO messages (sender_name, is_anonymous, role_relation, avatar_emoji, hint, message, status, source, likes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'admin', 0)"
            );
            $stmt->execute([
                $formValues['sender_name'], $formValues['is_anonymous'], $formValues['role_relation'],
                $formValues['avatar_emoji'], $formValues['hint'], $formValues['message'], $formValues['status'],
            ]);
            flash_set('success', 'Pesan baru berhasil ditambahkan.');
        }

        redirect('index.php');
    }
}

$adminBase = '../';
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

  <form method="post" action="form.php<?= $message ? '?id=' . (int) $message['id'] : '' ?>">
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
      <label for="status">Status</label>
      <select class="admin-select" id="status" name="status">
        <option value="approved" <?= $formValues['status'] === 'approved' ? 'selected' : '' ?>>Approved (langsung tayang)</option>
        <option value="pending" <?= $formValues['status'] === 'pending' ? 'selected' : '' ?>>Pending (menunggu review)</option>
        <option value="rejected" <?= $formValues['status'] === 'rejected' ? 'selected' : '' ?>>Rejected (ditolak)</option>
      </select>
    </div>

    <button type="submit" class="admin-btn"><?= $message ? 'Simpan Perubahan' : 'Tambah Pesan' ?></button>
    <a href="index.php" class="admin-btn admin-btn--secondary">Batal</a>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
