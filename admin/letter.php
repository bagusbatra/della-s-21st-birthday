<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';

$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require_valid();

    $salutation = trim($_POST['salutation'] ?? '');
    $closing = trim($_POST['closing'] ?? '');
    $sender = trim($_POST['sender'] ?? '');
    $bodyRaw = trim($_POST['paragraphs'] ?? '');

    // Paragraf dipisah oleh baris kosong, sama seperti format lama.
    $paragraphs = array_values(array_filter(
        array_map('trim', preg_split('/\r?\n\r?\n+/', $bodyRaw)),
        fn ($p) => $p !== ''
    ));

    $stmt = $pdo->prepare(
        'UPDATE love_letter SET salutation = ?, paragraphs_json = ?, closing = ?, sender = ? WHERE id = 1'
    );
    $stmt->execute([
        $salutation,
        json_encode($paragraphs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        $closing,
        $sender,
    ]);

    flash_set('success', 'Surat cinta berhasil diperbarui.');
    redirect('letter.php');
}

$loveLetter = $pdo->query('SELECT * FROM love_letter WHERE id = 1')->fetch();
$paragraphs = $loveLetter ? json_decode($loveLetter['paragraphs_json'], true) : [];
$paragraphsText = implode("\n\n", $paragraphs);

$pageTitle = 'Love Letter';
$activeMenu = 'letter';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
  <h2>Edit Surat Cinta</h2>
  <p class="admin-card--muted">Konten ini tampil di modal "Surat Cinta Rahasia" pada situs publik.</p>

  <form method="post" action="letter.php">
    <?= csrf_field() ?>

    <div class="admin-form-group">
      <label for="salutation">Salam Pembuka</label>
      <input class="admin-input" type="text" id="salutation" name="salutation" value="<?= e($loveLetter['salutation'] ?? '') ?>" required>
      <span class="hint">Contoh: Untuk Kekasih Terindahku, Della Puspa Ardiati,</span>
    </div>

    <div class="admin-form-group">
      <label for="paragraphs">Isi Surat (pisahkan tiap paragraf dengan baris kosong)</label>
      <textarea class="admin-textarea" id="paragraphs" name="paragraphs" rows="14" required><?= e($paragraphsText) ?></textarea>
      <span class="hint">Setiap paragraf akan tampil terpisah di surat. Beri satu baris kosong di antara paragraf.</span>
    </div>

    <div class="admin-form-group">
      <label for="closing">Kalimat Penutup</label>
      <input class="admin-input" type="text" id="closing" name="closing" value="<?= e($loveLetter['closing'] ?? '') ?>" required>
      <span class="hint">Contoh: Dengan segenap cinta dan ketulusan hati,</span>
    </div>

    <div class="admin-form-group">
      <label for="sender">Nama Pengirim</label>
      <input class="admin-input" type="text" id="sender" name="sender" value="<?= e($loveLetter['sender'] ?? '') ?>" required>
      <span class="hint">Contoh: Kekasihmu yang Selalu Menyayangimu ❤️</span>
    </div>

    <button type="submit" class="admin-btn">Simpan Perubahan</button>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
