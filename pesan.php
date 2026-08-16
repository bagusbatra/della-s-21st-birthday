<?php
/**
 * Halaman publik untuk teman-teman Della mengirim ucapan/doa,
 * dengan opsi anonim. Setiap kiriman masuk sebagai 'pending' dan
 * baru tampil di index.php setelah disetujui lewat admin panel.
 *
 * Proteksi spam berlapis (Iterasi 7 + pengerasan Iterasi 9):
 *   1. Honeypot field    — field tersembunyi, jebakan bot yang mengisi semua field.
 *   2. Time-trap         — submit dalam <2 detik dari render dianggap bot.
 *   3. Captcha matematika — soal penjumlahan ringan, jawaban divalidasi di session (server-side).
 *   4. Rate-limit per IP  — maksimal 5 pesan/jam per alamat IP.
 *   5. Rate-limit sesi    — jeda minimal 20 detik antar submit dari browser yang sama.
 *
 * (Lihat RENCANA-PENGEMBANGAN-ADMIN.md Iterasi 7 & 9.)
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';

ensure_session_started();

$pdo = get_pdo();
$errors = [];
$justSent = isset($_GET['sent']);

$formValues = [
    'sender_name' => '',
    'is_anonymous' => 0,
    'role_relation' => '',
    'avatar_emoji' => '',
    'hint' => '',
    'message' => '',
];

function pesan_new_captcha(): array
{
    $a = random_int(1, 9);
    $b = random_int(1, 9);
    $_SESSION['pesan_captcha_answer'] = $a + $b;
    $_SESSION['pesan_form_rendered_at'] = time();

    return [$a, $b];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Honeypot: field tersembunyi dari manusia, sering otomatis diisi bot.
    $honeypot = trim($_POST['website'] ?? '');

    $formValues['sender_name'] = trim($_POST['sender_name'] ?? '');
    $formValues['is_anonymous'] = isset($_POST['is_anonymous']) ? 1 : 0;
    $formValues['role_relation'] = mb_substr(trim($_POST['role_relation'] ?? ''), 0, 100);
    $formValues['avatar_emoji'] = mb_substr(trim($_POST['avatar_emoji'] ?? ''), 0, 8);
    $formValues['hint'] = mb_substr(trim($_POST['hint'] ?? ''), 0, 255);
    $formValues['message'] = trim($_POST['message'] ?? '');

    // 2. Time-trap: manusia butuh setidaknya beberapa detik untuk mengisi form.
    $renderedAt = (int) ($_SESSION['pesan_form_rendered_at'] ?? 0);
    $submittedTooFast = $renderedAt > 0 && (time() - $renderedAt) < 2;

    if ($honeypot !== '' || $submittedTooFast) {
        // Kemungkinan besar bot: pura-pura berhasil, jangan simpan apa pun.
        redirect('pesan.php?sent=1');
    }

    // 3. Captcha matematika (jawaban benar tersimpan di session, bukan di form).
    $captchaAnswer = $_POST['captcha_answer'] ?? '';
    if (!is_numeric($captchaAnswer) || (int) $captchaAnswer !== (int) ($_SESSION['pesan_captcha_answer'] ?? null)) {
        $errors[] = 'Jawaban captcha salah. Silakan coba lagi.';
    }

    if ($formValues['message'] === '') {
        $errors[] = 'Pesan tidak boleh kosong.';
    } elseif (mb_strlen($formValues['message']) > 1000) {
        $errors[] = 'Pesan maksimal 1000 karakter.';
    }

    if (!$formValues['is_anonymous'] && $formValues['sender_name'] === '') {
        $errors[] = 'Nama wajib diisi, atau centang "Kirim sebagai Anonim".';
    }

    if (mb_strlen($formValues['sender_name']) > 100) {
        $errors[] = 'Nama maksimal 100 karakter.';
    }

    // 5. Rate-limit sesi: jeda minimal 20 detik antar submit dari browser yang sama.
    if (!$errors) {
        $lastSubmitAt = (int) ($_SESSION['pesan_last_submit_at'] ?? 0);
        if ($lastSubmitAt > 0 && (time() - $lastSubmitAt) < 20) {
            $errors[] = 'Tunggu sebentar ya sebelum mengirim pesan lagi.';
        }
    }

    // 4. Rate-limit per IP: maksimal 5 pesan/jam.
    $ip = get_client_ip();

    if (!$errors) {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM messages
             WHERE ip_address = ? AND source = 'public_form' AND created_at > (NOW() - INTERVAL 1 HOUR)"
        );
        $stmt->execute([$ip]);
        $recentCount = (int) $stmt->fetchColumn();

        if ($recentCount >= 5) {
            $errors[] = 'Sudah banyak pesan terkirim dari jaringan ini dalam sejam terakhir. Coba lagi nanti ya.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO messages (sender_name, is_anonymous, role_relation, avatar_emoji, hint, message, status, source, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, 'pending', 'public_form', ?)"
        );
        $stmt->execute([
            $formValues['is_anonymous'] ? '' : $formValues['sender_name'],
            $formValues['is_anonymous'],
            $formValues['role_relation'],
            $formValues['avatar_emoji'] ?: '💌',
            $formValues['hint'],
            $formValues['message'],
            $ip,
        ]);

        $_SESSION['pesan_last_submit_at'] = time();
        unset($_SESSION['pesan_captcha_answer'], $_SESSION['pesan_form_rendered_at']);

        redirect('pesan.php?sent=1');
    }
}

// Selalu siapkan captcha baru untuk render form (baik kunjungan baru maupun retry setelah error).
[$captchaA, $captchaB] = pesan_new_captcha();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Titip Ucapan untuk Della · Della's 21st Birthday</title>
  <meta name="description" content="Kirim doa dan ucapan selamat ulang tahun ke-21 untuk Della Puspa Ardiati, boleh anonim." />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-[#fffafb] text-[#5d1c32] antialiased min-h-screen font-sans">
  <div class="pesan-page">
    <div class="max-w-2xl mx-auto px-4 py-12">
      <a href="index.php" class="text-xs text-[#a44a66] hover:text-[#5d1c32] font-medium">← Kembali ke Beranda</a>

      <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-[#ffe1e9] mt-4">
        <?php if ($justSent): ?>
          <div class="text-center py-6">
            <div class="w-16 h-16 mx-auto rounded-full bg-[#5d1c32] text-[#ffc2d1] flex items-center justify-center text-3xl shadow-lg border-2 border-[#ffe1e9] mb-4">
              💌
            </div>
            <h1 class="font-serif-elegant text-2xl sm:text-3xl text-[#5d1c32] font-bold mb-2">Terima Kasih! 🌸</h1>
            <p class="font-cormorant italic text-base sm:text-lg text-[#8a5d6c] mb-6">
              Ucapanmu sudah diterima dan sedang menunggu untuk ditampilkan di halaman utama Della.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
              <a href="pesan.php" class="px-5 py-2.5 rounded-full bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs shadow-xs">
                Kirim Ucapan Lain
              </a>
              <a href="index.php" class="px-5 py-2.5 rounded-full bg-white hover:bg-[#fdf2f8] text-[#5d1c32] border border-[#ffe1e9] font-medium text-xs shadow-xs">
                Kembali ke Beranda
              </a>
            </div>
          </div>
        <?php else: ?>
          <div class="text-center mb-6">
            <span class="text-3xl block mb-2">💌🌸</span>
            <h1 class="font-serif-elegant text-2xl sm:text-3xl text-[#5d1c32] font-bold">Titip Doa &amp; Ucapan untuk Della</h1>
            <p class="font-romantic text-2xl text-[#a44a66]">Selamat Ulang Tahun ke-21</p>
            <p class="text-xs sm:text-sm text-[#8a5d6c] mt-2">
              Tuliskan doa atau ucapan untuk Della Puspa Ardiati. Boleh pakai nama asli, boleh juga anonim —
              pesanmu akan direview dulu sebelum tampil di halaman utama.
            </p>
          </div>

          <?php if ($errors): ?>
            <div class="mb-4 p-3 bg-red-50 text-red-700 border border-red-200 rounded-xl text-xs">
              <?php foreach ($errors as $err): ?>
                <?= e($err) ?><br>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <form method="post" action="pesan.php" class="space-y-3 text-xs">
            <!-- Honeypot: dibiarkan kosong oleh manusia, sering terisi otomatis oleh bot -->
            <div class="pesan-honeypot" aria-hidden="true">
              <label for="website">Website</label>
              <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
              <label class="flex items-center gap-2 text-[#8a5d6c] mb-1">
                <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1" <?= $formValues['is_anonymous'] ? 'checked' : '' ?> style="accent-color:#5d1c32">
                Kirim sebagai Anonim
              </label>
            </div>

            <div>
              <label class="block text-[#8a5d6c] mb-1">Nama Kamu</label>
              <input
                type="text" name="sender_name" placeholder="Nama Anda"
                value="<?= e($formValues['sender_name']) ?>"
                class="w-full p-3 rounded-2xl border border-[#ffe1e9] bg-white text-xs text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"
              >
              <span class="block mt-1 text-[10px] text-[#8a5d6c]">Kosongkan &amp; centang "Kirim sebagai Anonim" kalau tidak ingin namamu ditampilkan.</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-[#8a5d6c] mb-1">Hubungan / Peran</label>
                <input
                  type="text" name="role_relation" placeholder="Sahabat / Bestie"
                  value="<?= e($formValues['role_relation']) ?>"
                  class="w-full p-3 rounded-2xl border border-[#ffe1e9] bg-white text-xs text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"
                >
              </div>
              <div>
                <label class="block text-[#8a5d6c] mb-1">Emoji Amplop</label>
                <input
                  type="text" name="avatar_emoji" placeholder="🌸"
                  value="<?= e($formValues['avatar_emoji']) ?>"
                  class="w-full p-3 rounded-2xl border border-[#ffe1e9] bg-white text-xs text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"
                >
              </div>
            </div>

            <div>
              <label class="block text-[#8a5d6c] mb-1">Petunjuk Rahasia (Hint)</label>
              <input
                type="text" name="hint" placeholder="Contoh: Pesan dari teman sebangkumu"
                value="<?= e($formValues['hint']) ?>"
                class="w-full p-3 rounded-2xl border border-[#ffe1e9] bg-white text-xs text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"
              >
              <span class="block mt-1 text-[10px] text-[#8a5d6c]">Petunjuk kecil yang tampil sebelum Della membuka amplopmu.</span>
            </div>

            <div>
              <label class="block text-[#8a5d6c] mb-1">Isi Doa &amp; Ucapan</label>
              <textarea
                name="message" rows="4" required maxlength="1000"
                placeholder="Tuliskan ucapan selamat ulang tahun ke-21 untuk Della..."
                class="w-full p-3 rounded-2xl border border-[#ffe1e9] bg-white text-xs text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"
              ><?= e($formValues['message']) ?></textarea>
            </div>

            <div>
              <label class="block text-[#8a5d6c] mb-1">Biar tahu kamu bukan robot: <?= $captchaA ?> + <?= $captchaB ?> = ?</label>
              <input
                type="text" inputmode="numeric" name="captcha_answer" required autocomplete="off"
                class="w-full p-3 rounded-2xl border border-[#ffe1e9] bg-white text-xs text-[#5d1c32] focus:outline-none focus:border-[#a44a66]"
              >
            </div>

            <button type="submit" class="w-full py-3 rounded-2xl bg-[#5d1c32] hover:bg-[#481426] text-white font-medium text-xs shadow-xs">
              Kirim Amplop Rahasia 💌
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
