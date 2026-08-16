<?php
/**
 * Proteksi CSRF sederhana berbasis session token.
 * Dipakai di semua form admin & (nanti) form publik pesan.php.
 */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Panggil di awal setiap handler POST. Menghentikan request dengan 403
 * kalau token tidak valid/tidak ada.
 */
function csrf_require_valid(): void
{
    $token = $_POST['csrf_token'] ?? null;

    if (!csrf_verify($token)) {
        http_response_code(403);
        die('Sesi form sudah kedaluwarsa atau tidak valid. Silakan muat ulang halaman dan coba lagi.');
    }
}
