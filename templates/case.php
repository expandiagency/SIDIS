<?php require __DIR__ . '/layout.php'; ?>

<main class="page">

  <section class="promo promo--case">
    <div class="promo__container">
      <?php if (!empty($case['logo_path'])): ?>
      <img src="<?= e(media_url($case['logo_path'])) ?>" alt="" class="promo__case-logo">
      <?php endif; ?>
      <h1 class="promo__title"><?= e($case['title']) ?></h1>
      <div class="promo__text"><?= e($case['description']) ?></div>
      <?php if (!empty($case['key_results'])): ?>
      <ul class="promo__results">
        <?php foreach ($case['key_results'] as $result): ?>
        <li class="promo__result"><?= e($result) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
      <?php if (!empty($case['image_path'])): ?>
      <img src="<?= e(media_url($case['image_path'])) ?>" alt="<?= e($case['title']) ?>" class="promo__case-img">
      <?php endif; ?>
    </div>
  </section>

  <section class="case-overview">
    <div class="case-overview__container">
      <h2 class="case-overview__title">Project Overview</h2>
      <div class="case-overview__text"><?= e($case['overview_text']) ?></div>
      <div class="case-overview__meta">
        <?php if (!empty($case['location'])): ?>
        <div><span>Location:</span> <?= e($case['location']) ?></div>
        <?php endif; ?>
        <?php if (!empty($case['cooperation_period'])): ?>
        <div><span>Cooperation:</span> <?= e($case['cooperation_period']) ?></div>
        <?php endif; ?>
        <?php if (!empty($case['terms'])): ?>
          <?php
          // Group terms by type
          $termsByType = [];
          foreach ($case['terms'] as $term) {
              $termsByType[$term['type']][] = $term['name'];
          }
          foreach ($termsByType as $type => $names):
          ?>
          <div><span><?= e($type) ?>:</span> <?= e(implode(', ', $names)) ?></div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($case['services'])): ?>
      <div class="case-overview__services">
        <?php foreach ($case['services'] as $service): ?>
        <span class="tag"><?= e($service['service_name']) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <?php if (!empty($case['challenges'])): ?>
  <section class="case-challenges">
    <div class="case-challenges__container">
      <h2 class="case-challenges__title">Challenges</h2>
      <div class="case-challenges__grid">
        <?php foreach ($case['challenges'] as $challenge): ?>
        <div class="case-challenge">
          <div class="case-challenge__title"><?= e($challenge['title']) ?></div>
          <div class="case-challenge__text"><?= e($challenge['text']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($case['tech_items'])): ?>
  <section class="case-tech">
    <div class="case-tech__container">
      <h2 class="case-tech__title">Tech Stack</h2>
      <div class="swiper case-tech__slider">
        <div class="swiper-wrapper">
          <?php foreach ($case['tech_items'] as $tech): ?>
          <div class="swiper-slide case-tech__item">
            <div class="case-tech__icon"><?= $tech['icon_svg'] ?></div>
            <div class="case-tech__name"><?= e($tech['name']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <button class="swiper-button-prev case-tech__prev"></button>
        <button class="swiper-button-next case-tech__next"></button>
      </div>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
