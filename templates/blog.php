<?php require __DIR__ . '/layout.php'; ?>

<main class="page">

  <section class="promo">
    <div class="promo__container">
      <h1 class="promo__title">Blog</h1>
    </div>
  </section>

  <section class="catalog">
    <div class="catalog__container">

      <!-- Filters -->
      <form class="catalog__filters" method="get" action="/blog/">
        <div class="sel-block">
          <select name="solution" class="sel-block__select" onchange="this.form.submit()">
            <option value="">All Solutions</option>
            <?php foreach ($terms['solutions'] as $term): ?>
            <option value="<?= e($term['id']) ?>"<?= (isset($_GET['solution']) && $_GET['solution'] == $term['id']) ? ' selected' : '' ?>><?= e($term['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="sel-block">
          <select name="department" class="sel-block__select" onchange="this.form.submit()">
            <option value="">All Departments</option>
            <?php foreach ($terms['departments'] as $term): ?>
            <option value="<?= e($term['id']) ?>"<?= (isset($_GET['department']) && $_GET['department'] == $term['id']) ? ' selected' : '' ?>><?= e($term['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="sel-block">
          <select name="industry" class="sel-block__select" onchange="this.form.submit()">
            <option value="">All Industries</option>
            <?php foreach ($terms['industries'] as $term): ?>
            <option value="<?= e($term['id']) ?>"<?= (isset($_GET['industry']) && $_GET['industry'] == $term['id']) ? ' selected' : '' ?>><?= e($term['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <noscript><button type="submit" class="button">Apply</button></noscript>
      </form>

      <!-- Posts grid -->
      <div class="catalog__grid" id="catalog-grid">
        <?php if (!empty($posts)): ?>
          <?php foreach ($posts as $post): ?>
          <a href="/blog/<?= e($post['slug']) ?>/" class="catalog__item catalog__item--post">
            <?php if (!empty($post['image_path'])): ?>
            <div class="catalog__item-img-wrap">
              <img src="<?= e(media_url($post['image_path'])) ?>" alt="<?= e($post['title']) ?>" class="catalog__item-img">
            </div>
            <?php endif; ?>
            <div class="catalog__item-body">
              <?php if (!empty($post['tags'])): ?>
              <div class="catalog__item-tags">
                <?php foreach ($post['tags'] as $tag): ?>
                <span class="tag"><?= e($tag) ?></span>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
              <div class="catalog__item-title"><?= e($post['title']) ?></div>
              <?php if (!empty($post['excerpt'])): ?>
              <div class="catalog__item-text"><?= e($post['excerpt']) ?></div>
              <?php endif; ?>
              <div class="catalog__item-author">
                <?php if (!empty($post['author_image_path'])): ?>
                <img src="<?= e(media_url($post['author_image_path'])) ?>" alt="<?= e($post['author_name']) ?>" class="catalog__item-avatar">
                <?php endif; ?>
                <span><?= e($post['author_name']) ?></span>
                <?php if (!empty($post['published_at'])): ?>
                <span class="catalog__item-date"><?= e(date('M d, Y', strtotime($post['published_at']))) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="catalog__empty">No posts found.</p>
        <?php endif; ?>
      </div>

    </div>
  </section>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
