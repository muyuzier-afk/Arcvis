<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;
use App\Core\View;

if (!is_installed()) {
    redirect(url('install.php'));
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Auth::attempt((string) $_POST['username'], (string) $_POST['password'])) {
        redirect(url('admin/index.php'));
    }
    $error = t('admin.login_failed');
}
ob_start(); ?>
<h1><?= e(t('admin.heading')) ?></h1>
<?php if ($error): ?><div class="notice"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="panel" style="max-width:420px;">
  <label><?= e(t('label.username')) ?><input name="username" required></label>
  <label><?= e(t('label.password')) ?><input type="password" name="password" required></label>
  <button type="submit"><?= e(t('button.login')) ?></button>
</form>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
