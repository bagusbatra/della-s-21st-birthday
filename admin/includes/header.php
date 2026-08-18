<?php
/**
 * Layout header admin. Include setelah require_admin_login().
 *
 * Variabel yang bisa di-set sebelum include ini:
 *   $pageTitle   (string) judul halaman, tampil di topbar & <title>
 *   $activeMenu  (string) key menu aktif, lihat array $menuItems di bawah
 *
 * Semua href menu/logout/stylesheet dibangun dari admin_base_url() (absolut,
 * mis. "/admin/") supaya benar di halaman manapun — termasuk di dalam
 * admin/gallery/ dan admin/messages/ — tanpa perlu set variabel base manual
 * per halaman lagi.
 */

require_once __DIR__ . '/../../includes/icons.php';

$adminBaseUrl = admin_base_url();
$siteBaseUrl = preg_replace('#/admin/$#', '/', $adminBaseUrl);
$pageTitle = $pageTitle ?? 'Admin';
$activeMenu = $activeMenu ?? '';
$admin = current_admin();

$sidebarPendingCount = (int) get_pdo()->query("SELECT COUNT(*) FROM messages WHERE status = 'pending'")->fetchColumn();

$menuItems = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '', 'icon' => 'home'],
    ['key' => 'hero', 'label' => 'Hero Section', 'href' => 'hero', 'icon' => 'mail'],
    ['key' => 'gate', 'label' => 'Gate & Countdown', 'href' => 'gate-settings', 'icon' => 'hourglass'],
    ['key' => 'cake', 'label' => 'Cake Section', 'href' => 'cake', 'icon' => 'cake'],
    ['key' => 'gallery', 'label' => 'Gallery / Memories', 'href' => 'gallery/', 'icon' => 'image'],
    ['key' => 'letter', 'label' => 'Love Letter', 'href' => 'letter', 'icon' => 'heart'],
    ['key' => 'messages', 'label' => 'Wishes & Messages', 'href' => 'messages/', 'icon' => 'message-circle', 'badge' => $sidebarPendingCount],
    ['key' => 'settings', 'label' => 'Settings Admin', 'href' => 'settings', 'icon' => 'settings'],
];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> · Admin Della's 21st Birthday</title>
  <link rel="stylesheet" href="<?= e($siteBaseUrl) ?>assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-shell">
    <div id="admin-sidebar-overlay" class="admin-sidebar-overlay"></div>
    <aside id="admin-sidebar" class="admin-sidebar">
      <div class="admin-sidebar__brand">
        <strong>Della's 21st Birthday</strong>
        <span>Admin Panel</span>
      </div>
      <ul class="admin-nav">
        <?php foreach ($menuItems as $item): ?>
          <li>
            <a href="<?= e($adminBaseUrl . $item['href']) ?>" class="<?= $activeMenu === $item['key'] ? 'active' : '' ?>">
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
        <div style="display:flex; align-items:center; gap:12px">
          <button type="button" id="btn-admin-menu" class="admin-menu-toggle" aria-label="Buka menu">
            <?= icon('menu', 'icon') ?>
          </button>
          <h1><?= e($pageTitle) ?></h1>
        </div>
        <div class="admin-topbar__user">
          <span><?= icon('user', 'icon icon-sm') ?> <?= e($admin['display_name'] ?: $admin['username']) ?></span>
          <a href="<?= e($adminBaseUrl) ?>logout">Keluar</a>
        </div>
      </header>

      <main class="admin-content">
        <?php $flash = flash_get(); ?>
        <?php if ($flash): ?>
          <div class="admin-flash admin-flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
