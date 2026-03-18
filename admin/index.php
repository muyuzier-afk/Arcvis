<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;
use App\Core\View;

Auth::requireAdmin();
$user = Auth::user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete_post' && $id > 0) {
        db()?->prepare('DELETE FROM posts WHERE id = :id')->execute(['id' => $id]);
        flash('notice', t('admin.deleted'));
        redirect(url('admin/index.php'));
    }

    if ($action === 'delete_work' && $id > 0) {
        db()?->prepare('DELETE FROM works WHERE id = :id')->execute(['id' => $id]);
        flash('notice', t('admin.deleted'));
        redirect(url('admin/index.php'));
    }
}

$posts = db()?->query('SELECT id, title, locale, status, updated_at FROM posts ORDER BY id DESC LIMIT 10')->fetchAll() ?? [];
$works = db()?->query('SELECT id, title, locale, url, updated_at FROM works ORDER BY id DESC LIMIT 10')->fetchAll() ?? [];
ob_start(); ?>
<h1><?= e(t('admin.heading')) ?></h1>
<p class="intro"><?= e(t('admin.welcome', ['name' => $user['display_name'] ?? 'admin'])) ?></p>
<div class="nav" style="margin-bottom:20px;">
  <a class="button" href="<?= e(url('admin/post_edit.php')) ?>"><?= e(t('button.create_post')) ?></a>
  <a class="button alt" href="<?= e(url('admin/work_edit.php')) ?>"><?= e(t('button.create_work')) ?></a>
  <a class="button alt" href="<?= e(url('admin/settings.php')) ?>"><?= e(t('admin.settings')) ?></a>
  <a class="button alt" href="<?= e(url('admin/logout.php')) ?>"><?= e(t('button.logout')) ?></a>
</div>
<div class="grid">
  <section class="panel">
    <div class="section-title"><?= e(t('admin.posts')) ?></div>
    <table>
      <tr><th>ID</th><th><?= e(t('label.title')) ?></th><th><?= e(t('label.locale')) ?></th><th><?= e(t('label.status')) ?></th><th><?= e(t('label.actions')) ?></th></tr>
      <?php if ($posts): foreach ($posts as $post): ?>
        <tr>
          <td><?= (int) $post['id'] ?></td>
          <td><?= e($post['title']) ?></td>
          <td><?= e($post['locale']) ?></td>
          <td><?= e($post['status']) ?></td>
          <td>
            <a href="<?= e(url('admin/post_edit.php')) ?>?id=<?= (int) $post['id'] ?>"><?= e(t('button.edit')) ?></a>
            <form method="post" style="display:inline;" onsubmit="return confirm('<?= e(t('confirm.delete')) ?>');">
              <input type="hidden" name="action" value="delete_post">
              <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
              <button class="button alt" type="submit"><?= e(t('button.delete')) ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="5"><?= e(t('empty.table')) ?></td></tr>
      <?php endif; ?>
    </table>
  </section>
  <section class="panel">
    <div class="section-title"><?= e(t('admin.works')) ?></div>
    <table>
      <tr><th>ID</th><th><?= e(t('label.title')) ?></th><th><?= e(t('label.locale')) ?></th><th><?= e(t('label.url')) ?></th><th><?= e(t('label.actions')) ?></th></tr>
      <?php if ($works): foreach ($works as $work): ?>
        <tr>
          <td><?= (int) $work['id'] ?></td>
          <td><?= e($work['title']) ?></td>
          <td><?= e($work['locale']) ?></td>
          <td><a href="<?= e($work['url']) ?>" target="_blank" rel="noreferrer"><?= e($work['url']) ?></a></td>
          <td>
            <a href="<?= e(url('admin/work_edit.php')) ?>?id=<?= (int) $work['id'] ?>"><?= e(t('button.edit')) ?></a>
            <form method="post" style="display:inline;" onsubmit="return confirm('<?= e(t('confirm.delete')) ?>');">
              <input type="hidden" name="action" value="delete_work">
              <input type="hidden" name="id" value="<?= (int) $work['id'] ?>">
              <button class="button alt" type="submit"><?= e(t('button.delete')) ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="5"><?= e(t('empty.table')) ?></td></tr>
      <?php endif; ?>
    </table>
  </section>
</div>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
