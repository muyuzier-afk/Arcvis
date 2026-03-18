<?php

declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('CONFIG_FILE', STORAGE_PATH . '/config.php');

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($path)) {
        require_once $path;
    }
});

require_once BASE_PATH . '/app/Support/helpers.php';

use App\Core\App;
use App\Support\Translator;

$app = new App();
$app->boot();

function app(): App
{
    global $app;
    return $app;
}

function config(string $key, mixed $default = null): mixed
{
    return app()->config($key, $default);
}

function db(): ?PDO
{
    return app()->db();
}

function t(string $key, array $replace = []): string
{
    return Translator::get($key, $replace, app()->locale());
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function is_installed(): bool
{
    return is_file(CONFIG_FILE);
}
