<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\View;

$works = [];
if (db()) {
    $stmt = db()->prepare("SELECT * FROM works WHERE is_visible = 1 AND locale = :locale ORDER BY sort_order ASC, id DESC");
    $stmt->execute(['locale' => app()->locale()]);
    $works = $stmt->fetchAll();
}

ob_start();
?>
<h1><?= e(t('nav.works')) ?></h1>
<div class="panel list">
<?php if ($works): foreach ($works as $work): ?>
  <article class="item">
    <a href="<?= e($work['url']) ?>" target="_blank" rel="noreferrer"><strong><?= e($work['title']) ?></strong></a>
    <p class="plain-text"><?= e($work['description']) ?></p>
  </article>
<?php endforeach; else: ?>
  <p class="plain-text"><?= e(t('empty.works')) ?></p>
<?php endif; ?>
</div>
<?php View::render('blank', ['content' => (string) ob_get_clean()]);
