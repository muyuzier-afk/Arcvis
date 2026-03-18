<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\View;

$posts = [];
$works = [];
if (db()) {
    $postStmt = db()->prepare("SELECT * FROM posts WHERE status = 'published' AND locale = :locale ORDER BY published_at DESC, id DESC LIMIT 5");
    $postStmt->execute(['locale' => app()->locale()]);
    $posts = $postStmt->fetchAll();

    $workStmt = db()->prepare("SELECT * FROM works WHERE is_visible = 1 AND locale = :locale ORDER BY sort_order ASC, id DESC LIMIT 6");
    $workStmt->execute(['locale' => app()->locale()]);
    $works = $workStmt->fetchAll();
}

ob_start();
?>
<h1><?= e(t('hero.heading')) ?></h1>
<p class="intro"><?= e(t('hero.copy')) ?></p>
<div class="grid">
  <section class="panel">
    <div class="section-title"><?= e(t('section.posts')) ?></div>
    <div class="list">
      <?php if ($posts): foreach ($posts as $post): ?>
        <article class="item">
          <div class="meta"><?= e((string) ($post['published_at'] ?? $post['created_at'])) ?></div>
          <a class="work-link" href="<?= e(url('post.php')) ?>?slug=<?= urlencode($post['slug']) ?>"><strong><?= e($post['title']) ?></strong></a>
          <p class="plain-text"><?= e($post['excerpt']) ?></p>
        </article>
      <?php endforeach; else: ?>
        <p class="plain-text"><?= e(t('empty.posts')) ?></p>
      <?php endif; ?>
    </div>
  </section>
  <section class="panel">
    <div class="section-title"><?= e(t('section.works')) ?></div>
    <div class="list">
      <?php if ($works): foreach ($works as $work): ?>
        <article class="item">
          <a href="<?= e($work['url']) ?>" target="_blank" rel="noreferrer"><strong><?= e($work['title']) ?></strong></a>
          <p class="plain-text"><?= e($work['description']) ?></p>
        </article>
      <?php endforeach; else: ?>
        <p class="plain-text"><?= e(t('empty.works')) ?></p>
      <?php endif; ?>
    </div>
  </section>
</div>
<?php
View::render('blank', ['content' => (string) ob_get_clean()]);
