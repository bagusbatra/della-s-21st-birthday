<?php
/**
 * Layout header admin. Include setelah require_admin_login().
 *
 * Variabel yang bisa di-set sebelum include ini:
 *   $adminBase   (string) '' kalau file ada di admin/, '../' kalau di admin/sub/
 *   $pageTitle   (string) judul halaman, tampil di topbar & <title>
 *   $activeMenu  (string) key menu aktif, lihat array $menuItems di bawah
 */

$adminBase = $adminBase ?? '';
$pageTitle = $pageTitle ?? 'Admin';
$activeMenu = $activeMenu ?? '';
$admin = current_admin();

$menuItems = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => 'index.php', 'icon' => '🏠'],
    ['key' => 'hero', 'label' => 'Hero Section', 'href' => 'hero.php', 'icon' => '💌'],
    ['key' => 'gate', 'label' => 'Gate & Countdown', 'href' => 'gate-settings.php', 'icon' => '⏳'],
    ['key' => 'cake', 'label' => 'Cake Section', 'href' => 'cake.php', 'icon' => '🎂'],
    ['key' => 'gallery', 'label' => 'Gallery / Memories', 'href' => 'gallery/index.php', 'icon' => '🖼️'],
    ['key' => 'letter', 'label' => 'Love Letter', 'href' => 'letter.php', 'icon' => '✉️'],
    ['key' => 'messages', 'label' => 'Wishes & Messages', 'href' => 'messages/index.php', 'icon' => '💬'],
    ['key' => 'settings', 'label' => 'Settings Admin', 'href' => 'settings.php', 'icon' => '⚙️'],
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
              <span><?= $item['icon'] ?></span>
              <span><?= e($item['label']) ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <div class="admin-main">
      <header class="admin-topbar">
        <h1><?= e($pageTitle) ?></h1>
        <div class="admin-topbar__user">
          <span>👤 <?= e($admin['display_name'] ?: $admin['username']) ?></span>
          <a href="<?= e($adminBase) ?>logout.php">Keluar</a>
        </div>
      </header>

      <main class="admin-content">
        <?php $flash = flash_get(); ?>
        <?php if ($flash): ?>
          <div class="admin-flash admin-flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
