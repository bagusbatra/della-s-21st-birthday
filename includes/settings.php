<?php
/**
 * Helper untuk tabel `settings` (key-value: Hero, Cake, Gate, Footer, dst).
 * require_once config/db.php SEBELUM file ini (butuh get_pdo()).
 */

function settings_all(): array
{
    static $cache = null;

    if ($cache === null) {
        $rows = get_pdo()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        $cache = [];
        foreach ($rows as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $cache;
}

function settings_get(string $key, string $default = ''): string
{
    $all = settings_all();

    return $all[$key] ?? $default;
}

function settings_set(string $key, string $value): void
{
    $stmt = get_pdo()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
}

/** @param array<string,string> $pairs */
function settings_set_many(array $pairs): void
{
    foreach ($pairs as $key => $value) {
        settings_set($key, $value);
    }
}
