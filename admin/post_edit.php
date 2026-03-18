<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;
use App\Core\View;

Auth::requireAdmin();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$post = ['title' => '', 'slug' => '', 'excerpt' => '', 'body' => '', 'locale' => app()->locale(), 'status' => 'draft'];
if ($id > 0) {
    $stmt = db()?->prepare('SELECT * FROM posts WHERE id = :id');
    $stmt?->execute(['id' => $id]);
    $post = $stmt?->fetch() ?: $post;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'body' => trim($_POST['body'] ?? ''),
        'locale' => trim($_POST['locale'] ?? app()->locale()),
        'status' => trim($_POST['status'] ?? 'draft'),
    ];
    if ($id > 0) {
        db()?->prepare('UPDATE posts SET title=:title, slug=:slug, excerpt=:excerpt, body=:body, locale=:locale, status=:status, published_at = IF(:status = "published" AND published_at IS NULL, NOW(), published_at) WHERE id=:id')
            ->execute($data + ['id' => $id]);
    } else {
        db()?->prepare('INSERT INTO posts (title, slug, excerpt, body, locale, status, published_at) VALUES (:title, :slug, :excerpt, :body, :locale, :status, IF(:status = "published", NOW(), NULL))')
            ->execute($data);
    }
    flash('notice', t('admin.saved'));
    redirect(url('admin/index.php'));
}
ob_start(); ?>
<h1><?= e(t('button.create_post')) ?></h1>
<form method="post" class="panel">
  <div class="grid">
    <label><?= e(t('label.title')) ?><input name="title" value="<?= e($post['title']) ?>" required></label>
    <label><?= e(t('label.slug')) ?><input name="slug" value="<?= e($post['slug']) ?>" required></label>
    <label><?= e(t('label.locale')) ?><select name="locale"><option value="en" <?= $post['locale']==='en'?'selected':'' ?>>English</option><option value="zh-CN" <?= $post['locale']==='zh-CN'?'selected':'' ?>>简体中文</option></select></label>
    <label><?= e(t('label.status')) ?><select name="status"><option value="draft" <?= $post['status']==='draft'?'selected':'' ?>><?= e(t('status.draft')) ?></option><option value="published" <?= $post['status']==='published'?'selected':'' ?>><?= e(t('status.published')) ?></option></select></label>
  </div>
  <label><?= e(t('label.excerpt')) ?><textarea name="excerpt"><?= e($post['excerpt']) ?></textarea></label>
  <label><?= e(t('label.body')) ?><textarea name="body"><?= e($post['body']) ?></textarea></label>
  <div class="nav">
    <button type="submit"><?= e(t('button.save')) ?></button>
    <a class="button alt" href="<?= e(url('admin/index.php')) ?>"><?= e(t('button.back_admin')) ?></a>
  </div>
</form>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
