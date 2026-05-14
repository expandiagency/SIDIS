<?php require __DIR__ . '/layout.php'; ?>

<main class="page">

  <section class="article-promo">
    <div class="article-promo__container">
      <?php if (!empty($post['image_path'])): ?>
      <img src="<?= e(media_url($post['image_path'])) ?>" alt="<?= e($post['title']) ?>" class="article-promo__img">
      <?php endif; ?>
      <?php if (!empty($post['published_at'])): ?>
      <div class="article-promo__date"><?= e(date('M d, Y', strtotime($post['published_at']))) ?></div>
      <?php endif; ?>
      <h1 class="article-promo__title"><?= e($post['title']) ?></h1>
      <?php if (!empty($post['subtitle'])): ?>
      <div class="article-promo__subtitle"><?= e($post['subtitle']) ?></div>
      <?php endif; ?>
      <?php if (!empty($post['tags'])): ?>
      <div class="article-promo__tags">
        <?php foreach ($post['tags'] as $tag): ?>
        <span class="tag"><?= e($tag) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="article-promo__author">
        <?php if (!empty($post['author_image_path'])): ?>
        <img src="<?= e(media_url($post['author_image_path'])) ?>" alt="<?= e($post['author_name']) ?>" class="article-promo__avatar">
        <?php endif; ?>
        <div>
          <div class="article-promo__author-name">
            <?php if (!empty($post['author_linkedin'])): ?>
            <a href="<?= e($post['author_linkedin']) ?>" target="_blank" rel="noopener noreferrer"><?= e($post['author_name']) ?></a>
            <?php else: ?>
            <?= e($post['author_name']) ?>
            <?php endif; ?>
          </div>
          <?php if (!empty($post['author_title'])): ?>
          <div class="article-promo__author-title"><?= e($post['author_title']) ?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="article-body">
    <div class="article-body__container">
      <?php if (!empty($post['toc'])): ?>
      <aside class="article-body__aside">
        <nav class="article-toc">
          <?php foreach ($post['toc'] as $tocItem): ?>
          <a href="#<?= e($tocItem['anchor']) ?>" class="article-toc__link"><?= e($tocItem['title']) ?></a>
          <?php endforeach; ?>
        </nav>
      </aside>
      <?php endif; ?>
      <div class="article-body__content article-content">
        <?= $post['content'] ?>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
