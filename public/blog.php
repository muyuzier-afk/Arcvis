<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\View;

$posts = [];
if (db()) {
    $stmt = db()->prepare("SELECT * FROM posts WHERE status = 'published' AND locale = :locale ORDER BY published_at DESC, id DESC");
    $stmt->execute(['locale' => app()->locale()]);
    $posts = $stmt->fetchAll();
}

ob_start();
?>
<h1><?= e(t('nav.blog')) ?></h1>
<div class="panel list">
<?php if ($posts): foreach ($posts as $post): ?>
  <article class="item">
    <div class="meta"><?= e((string) ($post['published_at'] ?? $post['created_at'])) ?></div>
    <a href="<?= e(url('post.php')) ?>?slug=<?= urlencode($post['slug']) ?>"><strong><?= e($post['title']) ?></strong></a>
    <p class="plain-text"><?= e($post['excerpt']) ?></p>
  </article>
<?php endforeach; else: ?>
  <p class="plain-text"><?= e(t('empty.posts')) ?></p>
<?php endif; ?>
</div>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
