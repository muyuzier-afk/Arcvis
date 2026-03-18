<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\View;

$post = null;
if (db() && isset($_GET['slug'])) {
    $stmt = db()->prepare("SELECT * FROM posts WHERE slug = :slug AND status = 'published' LIMIT 1");
    $stmt->execute(['slug' => (string) $_GET['slug']]);
    $post = $stmt->fetch();
}

ob_start();
?>
<?php if ($post): ?>
  <h1><?= e($post['title']) ?></h1>
  <div class="meta"><?= e((string) ($post['published_at'] ?? $post['created_at'])) ?></div>
  <div class="panel plain-text"><?= nl2br(e($post['body'])) ?></div>
  <p style="margin-top:20px;"><a class="button alt" href="<?= e(url('blog.php')) ?>"><?= e(t('button.back_blog')) ?></a></p>
<?php else: ?>
  <h1>404</h1>
  <p class="plain-text"><?= e(t('post.not_found')) ?></p>
<?php endif; ?>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
