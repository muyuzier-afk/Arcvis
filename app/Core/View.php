<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require BASE_PATH . '/templates/' . $template . '.php';
        $content = (string) ob_get_clean();
        require BASE_PATH . '/templates/layout.php';
    }
}
