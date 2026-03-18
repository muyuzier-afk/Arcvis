<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function attempt(string $username, string $password): bool
    {
        $stmt = db()?->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt?->execute(['username' => $username]);
        $user = $stmt?->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'display_name' => $user['display_name'],
            'locale' => $user['locale'],
        ];

        return true;
    }

    public static function user(): ?array
    {
        return $_SESSION['admin_user'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_user']);
    }

    public static function requireAdmin(): void
    {
        if (!self::check()) {
            redirect(url('admin/login.php'));
        }
    }
}
