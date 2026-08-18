<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('./');
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
        $stmt = $pdo->prepare('SELECT photo_url FROM messages WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);

        if ($row && $row['photo_url'] && str_starts_with($row['photo_url'], DELLA_UPLOAD_URL . '/')) {
            $localPath = DELLA_UPLOAD_DIR . '/' . basename($row['photo_url']);
            if (is_file($localPath)) {
                @unlink($localPath);
            }
        }

        flash_set('success', 'Pesan berhasil dihapus.');
        break;
}

redirect('./');
