<?php
/**
 * Fungsi bantu umum, dipakai admin & (nanti) halaman publik.
 */

/** Shortcut escape output HTML (cegah XSS). Selalu pakai ini saat mencetak data dari DB/input user. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Flash message sederhana lewat session (tampil sekali lalu hilang). */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function get_client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/** Format "2 jam lalu" / "Baru saja" dari string datetime MySQL. */
function format_relative_time(string $datetime): string
{
    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return $datetime;
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'Baru saja';
    }
    if ($diff < 3600) {
        return floor($diff / 60) . ' menit lalu';
    }
    if ($diff < 86400) {
        return floor($diff / 3600) . ' jam lalu';
    }
    if ($diff < 86400 * 7) {
        return floor($diff / 86400) . ' hari lalu';
    }

    return format_indonesian_datetime($timestamp);
}

/**
 * Validasi & pindahkan file upload gambar ke assets/uploads/. Dipakai admin
 * (gallery, pesan manual) & halaman publik (pesan.php, foto amplop opsional).
 * Butuh konstanta DELLA_UPLOAD_DIR & DELLA_UPLOAD_URL (config/config.php).
 *
 * Return ['url' => string|null, 'error' => string|null]
 */
function process_image_upload(array $file, int $maxBytes = 5 * 1024 * 1024): array
{
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['url' => null, 'error' => 'Upload gagal (kode error: ' . $file['error'] . ').'];
    }

    if ($file['size'] > $maxBytes) {
        return ['url' => null, 'error' => 'Ukuran file maksimal ' . round($maxBytes / 1024 / 1024, 1) . 'MB.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return ['url' => null, 'error' => 'Format file harus jpg, jpeg, png, atau webp.'];
    }

    if (@getimagesize($file['tmp_name']) === false) {
        return ['url' => null, 'error' => 'File yang diupload bukan gambar yang valid.'];
    }

    if (!is_dir(DELLA_UPLOAD_DIR)) {
        mkdir(DELLA_UPLOAD_DIR, 0755, true);
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $ext;
    $destination = DELLA_UPLOAD_DIR . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['url' => null, 'error' => 'Gagal menyimpan file ke server.'];
    }

    return ['url' => DELLA_UPLOAD_URL . '/' . $filename, 'error' => null];
}

/** Format timestamp jadi "19 Agustus 2026, 00:00 WIB" tanpa perlu ext-intl. */
function format_indonesian_datetime(int $timestamp): string
{
    static $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $day = date('j', $timestamp);
    $month = $months[(int) date('n', $timestamp)];
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);

    return "{$day} {$month} {$year}, {$time} WIB";
}
