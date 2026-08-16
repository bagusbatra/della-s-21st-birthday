<?php
/**
 * Konfigurasi umum situs. Dipakai oleh index.php maupun admin/*.
 *
 * PENTING: require_once "config/db.php" SEBELUM file ini — DELLA_RELEASE_TIMESTAMP
 * sekarang dibaca dari tabel `settings` (key: release_timestamp), yang dikelola
 * lewat Admin > Gate & Countdown (Iterasi 2). Kalau baris belum ada / DB
 * bermasalah, jatuh ke tanggal default di bawah supaya situs tetap jalan.
 */

date_default_timezone_set('Asia/Jakarta');

define('DELLA_DEV_COOKIE', 'della_dev_mode');
define('DELLA_SITE_NAME', "Della's 21st Birthday");
define('DELLA_UPLOAD_DIR', __DIR__ . '/../assets/uploads');
define('DELLA_UPLOAD_URL', 'assets/uploads');
define('DELLA_RELEASE_TIMESTAMP_FALLBACK', strtotime('2026-08-19 00:00:00'));

$della_release_raw = null;
try {
    $stmt = get_pdo()->query("SELECT setting_value FROM settings WHERE setting_key = 'release_timestamp' LIMIT 1");
    $della_release_raw = $stmt->fetchColumn();
} catch (Throwable $e) {
    $della_release_raw = null;
}

$della_release_timestamp = $della_release_raw ? strtotime($della_release_raw) : false;

define(
    'DELLA_RELEASE_TIMESTAMP',
    $della_release_timestamp !== false ? $della_release_timestamp : DELLA_RELEASE_TIMESTAMP_FALLBACK
);

unset($della_release_raw, $della_release_timestamp);
