<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Core\View;

if (is_installed()) {
    flash('notice', t('install.already'));
    redirect(url('admin/login.php'));
}

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'host' => trim($_POST['db_host'] ?? '127.0.0.1'),
        'port' => trim($_POST['db_port'] ?? '3306'),
        'database' => trim($_POST['db_name'] ?? ''),
        'username' => trim($_POST['db_user'] ?? ''),
        'password' => (string) ($_POST['db_pass'] ?? ''),
    ];

    try {
        if ($payload['database'] === '' || $payload['username'] === '') {
            throw new RuntimeException('Database name and username are required.');
        }

        if (strlen((string) ($_POST['admin_password'] ?? '')) < 8) {
            throw new RuntimeException(t('install.password_hint'));
        }

        $pdo = Database::connect($payload);
        $schema = file_get_contents(BASE_PATH . '/data/schema.sql');
        $pdo->exec($schema ?: '');

        $pdo->prepare('INSERT INTO users (username, password_hash, display_name, locale) VALUES (:username, :hash, :display_name, :locale)')
            ->execute([
                'username' => trim($_POST['admin_username'] ?? 'admin'),
                'hash' => password_hash((string) ($_POST['admin_password'] ?? ''), PASSWORD_DEFAULT),
                'display_name' => trim($_POST['display_name'] ?? 'Administrator'),
                'locale' => trim($_POST['default_locale'] ?? 'en'),
            ]);

        $settingStmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach ([
            'site_name' => trim($_POST['site_name'] ?? 'Arcvis'),
            'tagline' => trim($_POST['tagline'] ?? ''),
            'default_locale' => trim($_POST['default_locale'] ?? 'en'),
        ] as $key => $value) {
            $settingStmt->execute(['key' => $key, 'value' => $value]);
        }

        $configExport = var_export([
            'app' => [
                'name' => trim($_POST['site_name'] ?? 'Arcvis'),
                'default_locale' => trim($_POST['default_locale'] ?? 'en'),
                'supported_locales' => ['en', 'zh-CN'],
            ],
            'db' => $payload,
        ], true);

        if (!is_dir(STORAGE_PATH)) {
            mkdir(STORAGE_PATH, 0775, true);
        }

        file_put_contents(CONFIG_FILE, "<?php\nreturn " . $configExport . ";\n");
        flash('notice', t('install.done'));
        redirect(url('admin/login.php'));
    } catch (Throwable $throwable) {
        $error = $throwable->getMessage();
    }
}

ob_start();
?>
<h1><?= e(t('install.heading')) ?></h1>
<p class="intro"><?= e(t('install.copy')) ?></p>
<?php if ($error): ?><div class="notice"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="panel">
  <div class="grid">
    <label><?= e(t('label.site_name')) ?><input name="site_name" value="Arcvis"></label>
    <label><?= e(t('label.tagline')) ?><input name="tagline" value="A monochrome archive."></label>
    <label><?= e(t('label.default_locale')) ?>
      <select name="default_locale"><option value="en">English</option><option value="zh-CN">简体中文</option></select>
    </label>
  </div>
  <div class="section-title">Database</div>
  <div class="grid">
    <label><?= e(t('label.db_host')) ?><input name="db_host" value="127.0.0.1"></label>
    <label><?= e(t('label.db_port')) ?><input name="db_port" value="3306"></label>
    <label><?= e(t('label.db_name')) ?><input name="db_name" required></label>
    <label><?= e(t('label.db_user')) ?><input name="db_user" required></label>
    <label><?= e(t('label.db_pass')) ?><input name="db_pass" type="password"></label>
  </div>
  <div class="section-title">Administrator</div>
  <div class="grid">
    <label><?= e(t('label.username')) ?><input name="admin_username" value="admin"></label>
    <label><?= e(t('label.password')) ?><input name="admin_password" type="password" minlength="8" required></label>
    <label><?= e(t('label.display_name')) ?><input name="display_name" value="Administrator"></label>
  </div>
  <p class="plain-text"><?= e(t('install.password_hint')) ?></p>
  <button type="submit"><?= e(t('button.install')) ?></button>
</form>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
