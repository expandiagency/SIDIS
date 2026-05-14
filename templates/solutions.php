<?php require __DIR__ . '/layout.php'; ?>

<main class="page">

  <section class="promo promo--solutions">
    <div class="promo__container">
      <h1 class="promo__title"><?= e($solution_page['title']) ?></h1>
      <?php if (!empty($solution_page['description'])): ?>
      <div class="promo__text"><?= e($solution_page['description']) ?></div>
      <?php endif; ?>
      <?php if (!empty($solution_page['btn1_text']) || !empty($solution_page['btn2_text'])): ?>
      <div class="promo__btns">
        <?php if (!empty($solution_page['btn1_text'])): ?>
        <a href="#contact" class="button promo__btn"><?= e($solution_page['btn1_text']) ?></a>
        <?php endif; ?>
        <?php if (!empty($solution_page['btn2_text'])): ?>
        <a href="#contact" class="button button--outline promo__btn"><?= e($solution_page['btn2_text']) ?></a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      <?php if (!empty($solution_page['image_path'])): ?>
      <img src="<?= e(media_url($solution_page['image_path'])) ?>" alt="<?= e($solution_page['title']) ?>" class="promo__img">
      <?php endif; ?>
    </div>
  </section>

  <?php if (!empty($solution_page['features'])): ?>
  <section class="features">
    <div class="features__container">
      <div class="swiper features__slider">
        <div class="swiper-wrapper">
          <?php foreach ($solution_page['features'] as $feature): ?>
          <div class="swiper-slide features__slide">
            <div class="features__slide-title"><?= e($feature['title']) ?></div>
            <div class="features__slide-text"><?= e($feature['text']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <button class="swiper-button-prev features__prev"></button>
        <button class="swiper-button-next features__next"></button>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($solution_page['items'])): ?>
  <section class="solutions-list">
    <div class="solutions-list__container">
      <div class="solutions-list__grid">
        <?php foreach ($solution_page['items'] as $item): ?>
        <div class="solutions-list__card">
          <?php if (!empty($item['icon_svg'])): ?>
          <div class="solutions-list__card-icon"><?= $item['icon_svg'] ?></div>
          <?php endif; ?>
          <div class="solutions-list__card-title"><?= e($item['title']) ?></div>
          <div class="solutions-list__card-text"><?= e($item['description']) ?></div>
          <?php if (!empty($item['btn_url'])): ?>
          <a href="<?= e($item['btn_url']) ?>" class="solutions-list__card-link"><?= e($item['btn_text']) ?></a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
