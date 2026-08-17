<?php
/**
 * Layout header admin. Include setelah require_admin_login().
 *
 * Variabel yang bisa di-set sebelum include ini:
 *   $adminBase   (string) '' kalau file ada di admin/, '../' kalau di admin/sub/
 *   $pageTitle   (string) judul halaman, tampil di topbar & <title>
 *   $activeMenu  (string) key menu aktif, lihat array $menuItems di bawah
 */

require_once __DIR__ . '/../../includes/icons.php';

$adminBase = $adminBase ?? '';
$pageTitle = $pageTitle ?? 'Admin';
$activeMenu = $activeMenu ?? '';
$admin = current_admin();

$sidebarPendingCount = (int) get_pdo()->query("SELECT COUNT(*) FROM messages WHERE status = 'pending'")->fetchColumn();

$menuItems = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => 'index.php', 'icon' => 'home'],
    ['key' => 'hero', 'label' => 'Hero Section', 'href' => 'hero.php', 'icon' => 'mail'],
    ['key' => 'gate', 'label' => 'Gate & Countdown', 'href' => 'gate-settings.php', 'icon' => 'hourglass'],
    ['key' => 'cake', 'label' => 'Cake Section', 'href' => 'cake.php', 'icon' => 'cake'],
    ['key' => 'gallery', 'label' => 'Gallery / Memories', 'href' => 'gallery/index.php', 'icon' => 'image'],
    ['key' => 'letter', 'label' => 'Love Letter', 'href' => 'letter.php', 'icon' => 'heart'],
    ['key' => 'messages', 'label' => 'Wishes & Messages', 'href' => 'messages/index.php', 'icon' => 'message-circle', 'badge' => $sidebarPendingCount],
    ['key' => 'settings', 'label' => 'Settings Admin', 'href' => 'settings.php', 'icon' => 'settings'],
];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> · Admin Della's 21st Birthday</title>
  <link rel="stylesheet" href="<?= e($adminBase) ?>../assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <div class="admin-sidebar__brand">
        <strong>Della's 21st Birthday</strong>
        <span>Admin Panel</span>
      </div>
      <ul class="admin-nav">
        <?php foreach ($menuItems as $item): ?>
          <li>
            <a href="<?= e($adminBase . $item['href']) ?>" class="<?= $activeMenu === $item['key'] ? 'active' : '' ?>">
              <span><?= icon($item['icon'], 'icon') ?></span>
              <span style="flex:1"><?= e($item['label']) ?></span>
              <?php if (!empty($item['badge'])): ?>
                <span class="admin-nav__badge"><?= (int) $item['badge'] ?></span>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <div class="admin-main">
      <header class="admin-topbar">
        <h1><?= e($pageTitle) ?></h1>
        <div class="admin-topbar__user">
          <span><?= icon('user', 'icon icon-sm') ?> <?= e($admin['display_name'] ?: $admin['username']) ?></span>
          <a href="<?= e($adminBase) ?>logout.php">Keluar</a>
        </div>
      </header>

      <main class="admin-content">
        <?php $flash = flash_get(); ?>
        <?php if ($flash): ?>
          <div class="admin-flash admin-flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
