<?php require __DIR__ . '/layout.php'; ?>

<main class="page">

  <!-- Hero section -->
  <section class="hero">
    <div class="hero__container">
      <div class="hero__content">
        <h1 class="hero__title"><?= e($home['hero_title']) ?></h1>
        <div class="hero__text"><?= e($home['hero_subtitle']) ?></div>
        <div class="hero__btns">
          <a href="<?= e($home['hero_btn1_url']) ?>" class="button hero__btn"><?= e($home['hero_btn1_text']) ?></a>
          <a href="<?= e($home['hero_btn2_url']) ?>" class="button button--outline hero__btn"><?= e($home['hero_btn2_text']) ?></a>
        </div>
      </div>
      <div class="hero__video">
        <video autoplay muted loop playsinline class="hero__video-el">
          <source src="<?= e(media_url($home['hero_video_path'])) ?>" type="video/mp4">
        </video>
      </div>
    </div>
  </section>

  <!-- Why section (Swiper carousel) -->
  <section class="why">
    <div class="why__container">
      <h2 class="why__title"><?= e($home['why_title']) ?></h2>
      <div class="swiper why__slider">
        <div class="swiper-wrapper">
          <?php foreach ($why_slides as $slide): ?>
          <div class="swiper-slide why__slide">
            <div class="why__slide-icon"><?= $slide['icon_svg'] ?></div>
            <div class="why__slide-title"><?= e($slide['title']) ?></div>
            <div class="why__slide-text"><?= e($slide['text']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="swiper-pagination why__pagination"></div>
        <button class="swiper-button-prev why__prev"></button>
        <button class="swiper-button-next why__next"></button>
      </div>
    </div>
  </section>

  <!-- Automation section -->
  <section class="automation">
    <div class="automation__container">
      <div class="automation__content">
        <h2 class="automation__title"><?= e($home['automation_title']) ?></h2>
        <div class="automation__text"><?= e($home['automation_text']) ?></div>
        <div class="automation__locations">
          <div class="automation__location">
            <div class="automation__location-title"><?= e($home['automation_loc1_title']) ?></div>
            <div class="automation__location-city"><?= e($home['automation_loc1_city']) ?></div>
          </div>
          <div class="automation__location">
            <div class="automation__location-title"><?= e($home['automation_loc2_title']) ?></div>
            <div class="automation__location-city"><?= e($home['automation_loc2_city']) ?></div>
          </div>
        </div>
      </div>

      <!-- Partner logos marquee -->
      <div class="automation__partners">
        <div class="automation__marquee">
          <?php foreach ($partners as $partner): ?>
          <img src="<?= e(media_url($partner['image_path'])) ?>" alt="<?= e($partner['alt_text']) ?>" class="automation__partner-logo">
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Automation images carousel -->
      <div class="swiper automation__slider">
        <div class="swiper-wrapper">
          <?php foreach ($auto_imgs as $img): ?>
          <div class="swiper-slide automation__slide">
            <img src="<?= e(media_url($img['image_path'])) ?>" alt="" class="automation__slide-img">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Solutions tabs section -->
  <section class="solutions">
    <div class="solutions__container">
      <h2 class="solutions__title"><?= e($home['solutions_title']) ?></h2>
      <div class="solutions__tabs">
        <button class="solutions__tab solutions__tab--active" data-tab="solutions">Solutions</button>
        <button class="solutions__tab" data-tab="departments">Departments</button>
        <button class="solutions__tab" data-tab="industries">Industries</button>
      </div>

      <?php foreach (['solutions', 'departments', 'industries'] as $tabKey): ?>
      <div class="solutions__panel swiper<?= $tabKey === 'solutions' ? ' solutions__panel--active' : '' ?>" data-tab-panel="<?= e($tabKey) ?>">
        <div class="swiper-wrapper">
          <?php foreach ($solutions_items[$tabKey] as $card): ?>
          <div class="swiper-slide solutions__card">
            <div class="solutions__card-icon"><?= $card['icon_svg'] ?></div>
            <div class="solutions__card-title"><?= e($card['title']) ?></div>
            <div class="solutions__card-text"><?= e($card['description']) ?></div>
            <a href="<?= e($card['btn_url']) ?>" class="solutions__card-btn"><?= e($card['btn_text']) ?></a>
          </div>
          <?php endforeach; ?>
        </div>
        <button class="swiper-button-prev solutions__prev"></button>
        <button class="swiper-button-next solutions__next"></button>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Projects (Featured cases) -->
  <section class="projects">
    <div class="projects__container">
      <div class="projects__head">
        <h2 class="projects__title"><?= e($home['projects_title']) ?></h2>
        <a href="<?= e($home['projects_btn_url']) ?>" class="button button--outline projects__btn"><?= e($home['projects_btn_text']) ?></a>
      </div>
      <div class="projects__grid">
        <?php foreach ($featured_cases as $case): ?>
        <a href="/cases/<?= e($case['slug']) ?>/" class="project-card">
          <div class="project-card__img-wrap">
            <img src="<?= e(media_url($case['image_path'])) ?>" alt="<?= e($case['title']) ?>" class="project-card__img">
          </div>
          <div class="project-card__body">
            <?php if (!empty($case['logo_path'])): ?>
            <img src="<?= e(media_url($case['logo_path'])) ?>" alt="" class="project-card__logo">
            <?php endif; ?>
            <div class="project-card__title"><?= e($case['title']) ?></div>
            <div class="project-card__text"><?= e($case['description']) ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Reviews carousel -->
  <section class="reviews">
    <div class="reviews__container">
      <h2 class="reviews__title"><?= e($home['reviews_title']) ?></h2>
      <div class="swiper reviews__slider">
        <div class="swiper-wrapper">
          <?php foreach ($reviews as $review): ?>
          <div class="swiper-slide reviews__slide">
            <div class="reviews__slide-quote"><?= e($review['quote']) ?></div>
            <div class="reviews__slide-text"><?= e($review['text']) ?></div>
            <div class="reviews__slide-author">
              <?php if (!empty($review['author_image_path'])): ?>
              <img src="<?= e(media_url($review['author_image_path'])) ?>" alt="<?= e($review['author_name']) ?>" class="reviews__slide-avatar">
              <?php endif; ?>
              <div class="reviews__slide-info">
                <div class="reviews__slide-name"><?= e($review['author_name']) ?></div>
                <div class="reviews__slide-title"><?= e($review['author_title']) ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="swiper-pagination reviews__pagination"></div>
        <button class="swiper-button-prev reviews__prev"></button>
        <button class="swiper-button-next reviews__next"></button>
      </div>
    </div>
  </section>

  <!-- CTA / Get in touch -->
  <section class="getintouch" id="contact">
    <div class="getintouch__container">
      <h2 class="getintouch__title"><?= e($home['cta_title']) ?></h2>
      <a href="<?= e($home['cta_btn_url']) ?>" class="button getintouch__btn"><?= e($home['cta_btn_text']) ?></a>
    </div>
  </section>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
