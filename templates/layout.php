<?php /** @var string $content */ ?>
<!DOCTYPE html>
<html lang="<?= e(app()->locale()) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e(setting('site_name', config('app.name', 'Arcvis'))) ?></title>
  <link rel="stylesheet" href="<?= e(url('style.css')) ?>">
</head>
<body>
<div class="site">
  <div class="shell">
    <header class="topbar">
      <div>
        <div class="section-title"><?= e(setting('site_name', config('app.name', 'Arcvis'))) ?></div>
        <div class="plain-text"><?= e(setting('tagline', t('site.tagline'))) ?></div>
      </div>
      <div>
        <nav class="nav">
          <a href="<?= e(url('index.php')) ?>"><?= e(t('nav.home')) ?></a>
          <a href="<?= e(url('blog.php')) ?>"><?= e(t('nav.blog')) ?></a>
          <a href="<?= e(url('works.php')) ?>"><?= e(t('nav.works')) ?></a>
          <a href="<?= e(url('admin/index.php')) ?>"><?= e(t('nav.admin')) ?></a>
          <?php if (!is_installed()): ?>
            <a href="<?= e(url('install.php')) ?>"><?= e(t('nav.install')) ?></a>
          <?php endif; ?>
        </nav>
        <div class="lang-switcher">
          <a href="?lang=en">EN</a>
          <a href="?lang=zh-CN">中文</a>
        </div>
      </div>
    </header>

    <?php if ($flash = flash('notice')): ?>
      <div class="notice"><?= e($flash) ?></div>
    <?php endif; ?>

    <?= $content ?>

    <footer class="footer">
      <div class="plain-text">[ <?= is_installed() ? e(t('status.installed')) : e(t('status.not_installed')) ?> ]</div>
      <div class="plain-text">PHP 8 + MySQL + i18n</div>
    </footer>
  </div>
</div>
</body>
</html>
