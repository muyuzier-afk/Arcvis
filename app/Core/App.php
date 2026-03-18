<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

final class App
{
    private array $config = [];
    private ?PDO $db = null;
    private string $locale = 'en';

    public function boot(): void
    {
        $this->config = $this->loadConfig();
        $this->locale = $this->resolveLocale();

        if (!empty($this->config['db'])) {
            $this->db = Database::connect($this->config['db']);
        }
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function db(): ?PDO
    {
        return $this->db;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    private function loadConfig(): array
    {
        if (!is_file(CONFIG_FILE)) {
            return [
                'app' => [
                    'name' => 'Arcvis',
                    'default_locale' => 'en',
                    'supported_locales' => ['en', 'zh-CN'],
                ],
            ];
        }

        $config = require CONFIG_FILE;
        if (!is_array($config)) {
            throw new RuntimeException('Invalid config file.');
        }

        return $config;
    }

    private function resolveLocale(): string
    {
        $supported = $this->config('app.supported_locales', ['en', 'zh-CN']);
        $candidate = $_GET['lang'] ?? $_SESSION['locale'] ?? $this->config('app.default_locale', 'en');

        if (!in_array($candidate, $supported, true)) {
            $candidate = $this->config('app.default_locale', 'en');
        }

        $_SESSION['locale'] = $candidate;
        return $candidate;
    }
}
