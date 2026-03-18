<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;
use App\Core\View;

Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()?->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach (['site_name', 'tagline', 'default_locale'] as $key) {
        $stmt?->execute(['key' => $key, 'value' => trim((string) ($_POST[$key] ?? ''))]);
    }
    flash('notice', t('admin.saved'));
    redirect(url('admin/settings.php'));
}
ob_start(); ?>
<h1><?= e(t('admin.settings')) ?></h1>
<form method="post" class="panel">
  <label><?= e(t('label.site_name')) ?><input name="site_name" value="<?= e(setting('site_name', 'Arcvis')) ?>"></label>
  <label><?= e(t('label.tagline')) ?><input name="tagline" value="<?= e(setting('tagline', t('site.tagline'))) ?>"></label>
  <label><?= e(t('label.default_locale')) ?><select name="default_locale"><option value="en" <?= setting('default_locale', 'en') === 'en' ? 'selected' : '' ?>>English</option><option value="zh-CN" <?= setting('default_locale', 'en') === 'zh-CN' ? 'selected' : '' ?>>简体中文</option></select></label>
  <div class="nav">
    <button type="submit"><?= e(t('button.save')) ?></button>
    <a class="button alt" href="<?= e(url('admin/index.php')) ?>"><?= e(t('button.back_admin')) ?></a>
  </div>
</form>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
