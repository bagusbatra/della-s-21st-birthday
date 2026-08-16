<?php
/**
 * Autentikasi admin berbasis session.
 * require_once file ini di setiap halaman admin/* SEBELUM output apa pun.
 */

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/csrf.php';

ensure_session_started();

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function current_admin(): ?array
{
    if (!is_admin_logged_in()) {
        return null;
    }

    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'] ?? '',
        'display_name' => $_SESSION['admin_display_name'] ?? '',
    ];
}

/**
 * Alamat /admin/ (web-root-absolute) dihitung dari SCRIPT_NAME, supaya
 * redirect ke login tetap benar dipanggil dari halaman admin di
 * subfolder mana pun (mis. admin/gallery/index.php), dan tetap benar
 * kalau seluruh proyek di-hosting di dalam subfolder (bukan web root).
 */
function admin_base_url(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/admin/index.php';
    $pos = strpos($scriptName, '/admin/');

    if ($pos === false) {
        return '/admin/';
    }

    return substr($scriptName, 0, $pos) . '/admin/';
}

/**
 * Panggil di paling atas setiap halaman admin (kecuali login.php).
 * Redirect ke login kalau belum login.
 */
function require_admin_login(): void
{
    if (!is_admin_logged_in()) {
        $redirectTo = $_SERVER['REQUEST_URI'] ?? '';
        $loginUrl = admin_base_url() . 'login.php';
        header('Location: ' . $loginUrl . ($redirectTo ? '?redirect=' . urlencode($redirectTo) : ''));
        exit;
    }
}

function admin_login(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_display_name'] = $admin['display_name'];
}

function admin_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
