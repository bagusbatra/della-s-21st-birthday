<?php
/**
 * Endpoint AJAX untuk popup "Harapan Ulang Tahun ke-21" di index.php
 * (muncul setelah 21 lilin ditiup). Anonim, tidak ada moderasi/tampilan
 * publik — hasilnya cuma bisa dibaca Della lewat Admin Panel.
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metode tidak diizinkan.']);
    exit;
}

$pdo = get_pdo();
$wishText = trim($_POST['wish_text'] ?? '');
$ip = get_client_ip();

if ($wishText === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Harapan tidak boleh kosong.']);
    exit;
}

if (mb_strlen($wishText) > 500) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Harapan maksimal 500 karakter.']);
    exit;
}

// Rate-limit sederhana: maksimal 10 harapan/jam per alamat IP.
$stmt = $pdo->prepare(
    'SELECT COUNT(*) FROM birthday_wishes WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)'
);
$stmt->execute([$ip]);

if ((int) $stmt->fetchColumn() >= 10) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Terlalu banyak harapan dari jaringan ini. Coba lagi nanti ya.']);
    exit;
}

$pdo->prepare('INSERT INTO birthday_wishes (wish_text, ip_address) VALUES (?, ?)')->execute([$wishText, $ip]);

echo json_encode(['ok' => true]);
