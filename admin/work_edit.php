<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;
use App\Core\View;

Auth::requireAdmin();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$work = ['title' => '', 'description' => '', 'url' => '', 'locale' => app()->locale(), 'sort_order' => 0, 'is_visible' => 1];
if ($id > 0) {
    $stmt = db()?->prepare('SELECT * FROM works WHERE id = :id');
    $stmt?->execute(['id' => $id]);
    $work = $stmt?->fetch() ?: $work;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'url' => trim($_POST['url'] ?? ''),
        'locale' => trim($_POST['locale'] ?? app()->locale()),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
    ];
    if ($id > 0) {
        db()?->prepare('UPDATE works SET title=:title, description=:description, url=:url, locale=:locale, sort_order=:sort_order, is_visible=:is_visible WHERE id=:id')
            ->execute($data + ['id' => $id]);
    } else {
        db()?->prepare('INSERT INTO works (title, description, url, locale, sort_order, is_visible) VALUES (:title, :description, :url, :locale, :sort_order, :is_visible)')
            ->execute($data);
    }
    flash('notice', t('admin.saved'));
    redirect(url('admin/index.php'));
}
ob_start(); ?>
<h1><?= e(t('button.create_work')) ?></h1>
<form method="post" class="panel">
  <div class="grid">
    <label><?= e(t('label.title')) ?><input name="title" value="<?= e($work['title']) ?>" required></label>
    <label><?= e(t('label.url')) ?><input name="url" value="<?= e($work['url']) ?>" required></label>
    <label><?= e(t('label.locale')) ?><select name="locale"><option value="en" <?= $work['locale']==='en'?'selected':'' ?>>English</option><option value="zh-CN" <?= $work['locale']==='zh-CN'?'selected':'' ?>>简体中文</option></select></label>
    <label><?= e(t('label.sort_order')) ?><input type="number" name="sort_order" value="<?= (int) $work['sort_order'] ?>"></label>
  </div>
  <label><?= e(t('label.description')) ?><textarea name="description"><?= e($work['description']) ?></textarea></label>
  <label><input type="checkbox" name="is_visible" value="1" <?= (int) $work['is_visible'] === 1 ? 'checked' : '' ?>> Visible</label>
  <div class="nav">
    <button type="submit"><?= e(t('button.save')) ?></button>
    <a class="button alt" href="<?= e(url('admin/index.php')) ?>"><?= e(t('button.back_admin')) ?></a>
  </div>
</form>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
