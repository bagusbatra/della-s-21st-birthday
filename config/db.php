<?php
/**
 * db.php — konfigurasi database.
 * Koneksi database (PDO + MySQL). Semua query di admin/publik wajib
 * pakai prepared statement lewat koneksi ini — jangan pernah
 * concat string user input langsung ke SQL.
 */

$localConfigPath = __DIR__ . '/config.local.php';

if (!file_exists($localConfigPath)) {
    http_response_code(500);
    die(
        'Konfigurasi database belum ada. Salin "config/config.local.php.example" ' .
        'menjadi "config/config.local.php" lalu sesuaikan isinya.'
    );
}

require_once $localConfigPath;

function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        DB_HOST,
        DB_PORT,
        DB_NAME
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die('Gagal terhubung ke database. Pastikan MySQL di Laragon sedang berjalan. (' . $e->getMessage() . ')');
    }

    return $pdo;
}
