<?php require __DIR__ . '/layout.php'; ?>

<main class="page">

  <section class="promo">
    <div class="promo__container">
      <h1 class="promo__title">Case Studies</h1>
    </div>
  </section>

  <section class="catalog">
    <div class="catalog__container">

      <!-- Filters -->
      <form class="catalog__filters" method="get" action="/cases/">
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

      <!-- Items grid -->
      <div class="catalog__grid" id="catalog-grid">
        <?php if (!empty($cases)): ?>
          <?php foreach ($cases as $case): ?>
          <a href="/cases/<?= e($case['slug']) ?>/" class="catalog__item">
            <div class="catalog__item-img-wrap">
              <img src="<?= e(media_url($case['image_path'])) ?>" alt="<?= e($case['title']) ?>" class="catalog__item-img">
            </div>
            <div class="catalog__item-body">
              <?php if (!empty($case['logo_path'])): ?>
              <img src="<?= e(media_url($case['logo_path'])) ?>" alt="" class="catalog__item-logo">
              <?php endif; ?>
              <div class="catalog__item-title"><?= e($case['title']) ?></div>
              <div class="catalog__item-text"><?= e($case['description']) ?></div>
              <?php if (!empty($case['location'])): ?>
              <div class="catalog__item-location"><?= e($case['location']) ?></div>
              <?php endif; ?>
            </div>
          </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="catalog__empty">No results found.</p>
        <?php endif; ?>
      </div>

    </div>
  </section>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
