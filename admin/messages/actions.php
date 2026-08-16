<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

csrf_require_valid();

$pdo = get_pdo();
$id = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'approve':
        $pdo->prepare("UPDATE messages SET status = 'approved' WHERE id = ?")->execute([$id]);
        flash_set('success', 'Pesan disetujui dan sekarang tayang di situs.');
        break;

    case 'reject':
        $pdo->prepare("UPDATE messages SET status = 'rejected' WHERE id = ?")->execute([$id]);
        flash_set('success', 'Pesan ditolak.');
        break;

    case 'delete':
        $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);
        flash_set('success', 'Pesan berhasil dihapus.');
        break;
}

redirect('index.php');
