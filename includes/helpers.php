<?php
/**
 * Fungsi bantu umum, dipakai admin & (nanti) halaman publik.
 */

/** Shortcut escape output HTML (cegah XSS). Selalu pakai ini saat mencetak data dari DB/input user. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Flash message sederhana lewat session (tampil sekali lalu hilang). */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function get_client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/** Format timestamp jadi "19 Agustus 2026, 00:00 WIB" tanpa perlu ext-intl. */
function format_indonesian_datetime(int $timestamp): string
{
    static $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $day = date('j', $timestamp);
    $month = $months[(int) date('n', $timestamp)];
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);

    return "{$day} {$month} {$year}, {$time} WIB";
}
