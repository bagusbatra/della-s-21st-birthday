<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

csrf_require_valid();

$pdo = get_pdo();
$id = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'toggle_publish':
        $pdo->prepare('UPDATE memories SET is_published = NOT is_published WHERE id = ?')->execute([$id]);
        flash_set('success', 'Status publish foto diperbarui.');
        break;

    case 'delete':
        $stmt = $pdo->prepare('SELECT image_url FROM memories WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
            $pdo->prepare('DELETE FROM memories WHERE id = ?')->execute([$id]);

            // Hapus file upload lokal juga (kalau bukan URL eksternal).
            if ($row['image_url'] && str_starts_with($row['image_url'], DELLA_UPLOAD_URL . '/')) {
                $localPath = DELLA_UPLOAD_DIR . '/' . basename($row['image_url']);
                if (is_file($localPath)) {
                    @unlink($localPath);
                }
            }

            flash_set('success', 'Foto berhasil dihapus.');
        }
        break;

    case 'move_up':
    case 'move_down':
        $rows = $pdo->query('SELECT id, sort_order FROM memories ORDER BY sort_order ASC, id ASC')->fetchAll();
        $index = null;

        foreach ($rows as $i => $row) {
            if ((int) $row['id'] === $id) {
                $index = $i;
                break;
            }
        }

        if ($index !== null) {
            $swapWith = $action === 'move_up' ? $index - 1 : $index + 1;

            if (isset($rows[$swapWith])) {
                $a = $rows[$index];
                $b = $rows[$swapWith];
                $pdo->prepare('UPDATE memories SET sort_order = ? WHERE id = ?')->execute([$b['sort_order'], $a['id']]);
                $pdo->prepare('UPDATE memories SET sort_order = ? WHERE id = ?')->execute([$a['sort_order'], $b['id']]);
            }
        }
        break;
}

redirect('index.php');
