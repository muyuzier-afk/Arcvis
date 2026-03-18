<?php

declare(strict_types=1);

function setting(string $key, ?string $default = null): ?string
{
    $stmt = db()?->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
    $stmt?->execute(['key' => $key]);
    $value = $stmt?->fetchColumn();

    return $value !== false ? (string) $value : $default;
}

function base_path_url(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($scriptName));

    if ($dir === '/' || $dir === '.') {
        return '';
    }

    if (str_ends_with($dir, '/admin')) {
        $dir = substr($dir, 0, -6) ?: '';
    }

    return rtrim($dir, '/');
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return base_path_url() . ($path === '/' ? '' : $path);
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $value;
}
