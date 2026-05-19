<?php
/**
 * Home page template — mirrors index.html structure exactly.
 * Dynamic blocks rendered in sort_order from home_blocks table.
 */
$h      = $home ?? [];           // home_content row
$blocks = $home_blocks ?? [];    // ordered list of active blocks

// Fallback: if no blocks configured, use default order
if (empty($blocks)) {
    $blocks = [
        ['block_key'=>'hero'],['block_key'=>'why'],['block_key'=>'automation'],
        ['block_key'=>'solutions'],['block_key'=>'projects'],['block_key'=>'reviews'],
        ['block_key'=>'roadmap'],['block_key'=>'getintouch'],['block_key'=>'presentation'],
        ['block_key'=>'faq'],['block_key'=>'articles'],
    ];
}

require __DIR__ . '/layout.php';
?>
<main class="page">
<?php foreach ($blocks as $block): ?>
<?php $bk = $block['block_key']; ?>

<?php /* ═══════ HERO ═══════════════════════════════════════════════════ */ ?>
<?php if ($bk === 'hero'): ?>
<section class="hero">
    <div class="hero__container">
        <div class="hero__body">
            <h1 class="hero__title title"><?= e($h['hero_title'] ?? 'Business process automation') ?></h1>
            <div class="hero__text"><?= nl2br(e($h['hero_subtitle'] ?? 'Many growing businesses lose time and money due to inefficient workflows and outdated systems.')) ?></div>
            <div class="hero__btns">
                <button type="button" class="hero__btn button"><?= e($h['hero_btn1_text'] ?? 'Try AI assistant') ?></button>
                <button type="button" class="hero__btn button button--icon">
                    <span class="button__text"><?= e($h['hero_btn2_text'] ?? 'Free audit') ?></span>
                    <span class="button__icon">
                        <svg width="14" height="14" viewbox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.56543 12.8L12.5654 0.799988M12.5654 0.799988L0.56543 0.799988M12.5654 0.799988V12.6868" stroke="currentColor" stroke-width="1.6"></path></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="hero__rating">
        <div class="hero__rating-body">
            <div class="hero__rating-item"><img src="./assets/img/header/Rating%20Container.svg" width="111" height="54" alt="Rating" loading="lazy"></div>
            <div class="hero__rating-item"><img src="./assets/img/header/Top%20Rated%20Badge.png" width="205" height="53" alt="Top Rated" loading="lazy"></div>
        </div>
    </div>
    <div class="hero__bg">
        <video autoplay muted loop playsinline>
            <source src="<?= e($h['hero_video_path'] ?? './assets/video/1-hero.mp4') ?>" type="video/mp4">
        </video>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ WHY ════════════════════════════════════════════════════ */ ?>
<?php if ($bk === 'why'): ?>
<section class="why">
    <div class="why__container">
        <div class="why__top">
            <div class="why__head">
                <h2 class="why__title title title--h1"><?= nl2br(e($h['why_title'] ?? "Why Businesses\nChoose RPA?")) ?></h2>
                <div class="why__descr">
                    <div class="why__subtitle"><?= e($h['why_subtitle'] ?? 'Robotic Process Automation') ?></div>
                    <div class="why__text"><?= nl2br(e($h['why_text'] ?? 'Most SMEs can save time and money with RPA by automating tasks, reducing errors, and improving system integration.')) ?></div>
                </div>
            </div>
            <div class="why__navigation">
                <button type="button" class="swiper-button-prev">
                    <svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M10.0527 0.523193L1.05273 8.52319L10.0527 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>
                </button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next">
                    <svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>
                </button>
            </div>
        </div>
        <div data-fls-slider="" class="why__slider swiper">
            <div class="why__wrapper swiper-wrapper">
                <?php if (!empty($why_slides)): ?>
                <?php foreach ($why_slides as $slide): ?>
                <div class="why__slide swiper-slide">
                    <div class="why__slide-icon"><?= $slide['icon_svg'] ?></div>
                    <div class="why__slide-title"><?= nl2br(e($slide['title'])) ?></div>
                    <div class="why__slide-text"><?= e($slide['text']) ?></div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="why__slide swiper-slide">
                    <div class="why__slide-icon"><svg width="54" height="42" viewbox="0 0 54 42" fill="none"><path d="M12.6 33.2938C11.8773 33.2938 11.1987 33.0121 10.6902 32.5018L0.7911 22.6026C0.2817 22.095 0 21.4165 0 20.6938C0 19.9711 0.2817 19.2925 0.792 18.784L10.6911 8.88485C11.1987 8.37545 11.8773 8.09375 12.6 8.09375C14.0886 8.09375 15.3 9.30515 15.3 10.7938C15.3 11.5165 15.0183 12.195 14.508 12.7035L6.5178 20.6938L14.5089 28.6849C15.0192 29.1925 15.3 29.8711 15.3 30.5938C15.3 32.0824 14.0886 33.2938 12.6 33.2938Z" fill="currentColor"></path><path d="M41.4031 34.2C39.9145 34.2 38.7031 32.9886 38.7031 31.5C38.7031 30.7773 38.9848 30.0987 39.4951 29.5902L47.4853 21.6L39.4942 13.6089C38.9848 13.0995 38.7031 12.4209 38.7031 11.7C38.7031 10.2114 39.9145 9 41.4031 9C42.1258 9 42.8044 9.2817 43.3129 9.792L53.212 19.6911C53.7223 20.2005 54.0031 20.8791 54.0031 21.6C54.0031 22.3227 53.7214 23.0013 53.2111 23.5098L43.312 33.4089C42.8044 33.9192 42.1258 34.2 41.4031 34.2Z" fill="currentColor"></path><path d="M18.9031 41.4C17.4145 41.4 16.2031 40.1886 16.2031 38.7C16.2031 38.3418 16.2733 37.9917 16.4101 37.6605L32.611 1.6587C33.034 0.6507 34.0123 0 35.1031 0C36.5917 0 37.8031 1.2114 37.8031 2.7C37.8031 3.0582 37.7329 3.4083 37.5961 3.7395L21.3952 39.7413C20.9722 40.7493 19.9939 41.4 18.9031 41.4Z" fill="currentColor"></path></svg></div>
                    <div class="why__slide-title">Manual Processes Kill<br>Productivity</div>
                    <div class="why__slide-text">RPA automates repetitive tasks instantly, saving 30–50% of time spent on admin.</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ AUTOMATION ═══════════════════════════════════════════ */ ?>
<?php if ($bk === 'automation'): ?>
<section class="automation">
    <div class="automation__container">
        <div class="automation__body">
            <div class="automation__content">
                <div class="automation__head">
                    <h2 class="automation__title title title--h2"><?= e($h['automation_title'] ?? 'We Build Future-Proof Automation') ?></h2>
                    <div class="automation__text"><?= nl2br(e($h['automation_text'] ?? '')) ?></div>
                </div>
                <div class="automation__bottom">
                    <div class="automation__location">
                        <div class="automation__subtitle">Location</div>
                        <div class="automation__items">
                            <div class="automation__item">
                                <div class="automation__item-icon"><svg width="22" height="30" viewbox="0 0 22 30" fill="none"><path d="M10.625 14.25C9.6186 14.25 8.65342 13.8549 7.94178 13.1517C7.23015 12.4484 6.83036 11.4946 6.83036 10.5C6.83036 9.50544 7.23015 8.55161 7.94178 7.84835C8.65342 7.14509 9.6186 6.75 10.625 6.75C11.6314 6.75 12.5966 7.14509 13.3082 7.84835C14.0199 8.55161 14.4196 9.50544 14.4196 10.5C14.4196 11.4946 14.0199 12.4484 13.3082 13.1517C12.5966 13.8549 11.6314 14.25 10.625 14.25ZM10.625 0C7.80707 0 5.10456 1.10625 3.11199 3.07538C1.11942 5.04451 0 7.71523 0 10.5C0 18.375 10.625 30 10.625 30C10.625 30 21.25 18.375 21.25 10.5C21.25 7.71523 20.1306 5.04451 18.138 3.07538C16.1454 1.10625 13.4429 0 10.625 0Z" fill="currentColor"></path></svg></div>
                                <div class="automation__item-body">
                                    <div class="automation__item-title"><?= e($h['automation_loc1_title'] ?? 'Estonia') ?></div>
                                    <div class="automation__item-text"><?= e($h['automation_loc1_city'] ?? 'Tallinn') ?></div>
                                </div>
                            </div>
                            <div class="automation__item">
                                <div class="automation__item-icon"><svg width="22" height="30" viewbox="0 0 22 30" fill="none"><path d="M10.625 14.25C9.6186 14.25 8.65342 13.8549 7.94178 13.1517C7.23015 12.4484 6.83036 11.4946 6.83036 10.5C6.83036 9.50544 7.23015 8.55161 7.94178 7.84835C8.65342 7.14509 9.6186 6.75 10.625 6.75C11.6314 6.75 12.5966 7.14509 13.3082 7.84835C14.0199 8.55161 14.4196 9.50544 14.4196 10.5C14.4196 11.4946 14.0199 12.4484 13.3082 13.1517C12.5966 13.8549 11.6314 14.25 10.625 14.25ZM10.625 0C7.80707 0 5.10456 1.10625 3.11199 3.07538C1.11942 5.04451 0 7.71523 0 10.5C0 18.375 10.625 30 10.625 30C10.625 30 21.25 18.375 21.25 10.5C21.25 7.71523 20.1306 5.04451 18.138 3.07538C16.1454 1.10625 13.4429 0 10.625 0Z" fill="currentColor"></path></svg></div>
                                <div class="automation__item-body">
                                    <div class="automation__item-title"><?= e($h['automation_loc2_title'] ?? 'Switzerland') ?></div>
                                    <div class="automation__item-text"><?= e($h['automation_loc2_city'] ?? 'Lausanne') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="automation__btns">
                        <button type="button" class="automation__btn button button--secondary button--dark-hover"><?= e($h['automation_btn1_text'] ?? 'Contact us') ?></button>
                        <button type="button" class="automation__btn button button--icon">
                            <span class="button__text"><?= e($h['automation_btn2_text'] ?? 'Free audit') ?></span>
                            <span class="button__icon"><svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.56543 12.8L12.5654 0.799988M12.5654 0.799988L0.56543 0.799988M12.5654 0.799988V12.6868" stroke="currentColor" stroke-width="1.6"></path></svg></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="automation__images">
                <div data-fls-slider="" class="automation__slider swiper">
                    <div class="automation__wrapper swiper-wrapper">
                        <?php if (!empty($auto_imgs)): ?>
                        <?php foreach ($auto_imgs as $img): ?>
                        <div class="automation__slide swiper-slide">
                            <img alt="Image" loading="lazy" src="<?= e(media_url($img['image_path'])) ?>">
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="automation__slide swiper-slide"><img alt="" loading="lazy" src="./assets/img/automation/image.webp"></div>
                        <div class="automation__slide swiper-slide"><img alt="" loading="lazy" src="./assets/img/promo/image-1.webp"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="automation__arrows">
                    <button type="button" class="swiper-button-prev"><svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M10.0527 0.523193L1.05273 8.52319L10.0527 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg></button>
                    <button type="button" class="swiper-button-next"><svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg></button>
                </div>
            </div>
        </div>
        <div data-fls-marquee="" data-fls-marquee-space="60" data-fls-marquee-speed="765" class="automation__marquee">
            <?php if (!empty($partners)): ?>
            <?php foreach ($partners as $logo): ?>
            <div class="automation__marquee-item"><img alt="<?= e($logo['alt_text']) ?>" loading="lazy" src="<?= e(media_url($logo['image_path'])) ?>"></div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="automation__marquee-item"><img alt="" loading="lazy" src="./assets/img/automation/partner-1.webp"></div>
            <div class="automation__marquee-item"><img alt="" loading="lazy" src="./assets/img/automation/partner-2.webp"></div>
            <div class="automation__marquee-item"><img alt="" loading="lazy" src="./assets/img/automation/partner-3.webp"></div>
            <div class="automation__marquee-item"><img alt="" loading="lazy" src="./assets/img/automation/partner-4.webp"></div>
            <div class="automation__marquee-item"><img alt="" loading="lazy" src="./assets/img/automation/partner-1.webp"></div>
            <div class="automation__marquee-item"><img alt="" loading="lazy" src="./assets/img/automation/partner-2.webp"></div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ SOLUTIONS ════════════════════════════════════════════ */ ?>
<?php if ($bk === 'solutions'): ?>
<section class="solutions">
    <div class="solutions__container">
        <div data-fls-tabs="" class="solutions__body">
            <div class="solutions__content">
                <div class="solutions__head">
                    <h2 class="solutions__title title title--h1"><?= e($h['solutions_title'] ?? 'Automation Solutions') ?></h2>
                    <div class="solutions__text"><?= nl2br(e($h['solutions_text'] ?? 'As automation specialists, we offer a wide range of solutions tailored to your business goals.')) ?></div>
                    <div class="solutions__categories" data-fls-tabs-titles="" data-switch-block="">
                        <button type="button" class="solutions__category --tab-active is-active">Solutions</button>
                        <button type="button" class="solutions__category">Departments</button>
                        <button type="button" class="solutions__category">Industries</button>
                    </div>
                </div>
                <div class="solutions__navigation">
                    <button type="button" class="swiper-button-prev"><svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M10.0527 0.523193L1.05273 8.52319L10.0527 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg></button>
                    <div class="swiper-pagination"></div>
                    <button type="button" class="swiper-button-next"><svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg></button>
                </div>
            </div>
            <div data-fls-tabs-body="" class="solutions__tabs">
                <?php foreach (['solutions'=>'solutions','departments'=>'department','industries'=>'industry'] as $tabName => $typeKey): ?>
                <?php $tab_items = $solutions_items[$tabName] ?? []; ?>
                <div class="solutions__tab"<?= $tabName !== 'solutions' ? ' hidden' : '' ?>>
                    <?php if (!empty($tab_items)): ?>
                    <div data-fls-slider="" class="solutions__slider swiper">
                        <div class="solutions__wrapper swiper-wrapper">
                            <?php foreach ($tab_items as $card): ?>
                            <div class="solutions__slide swiper-slide">
                                <div class="solutions__slide-body">
                                    <div class="solutions__slide-title"><?= e($card['title']) ?></div>
                                    <div class="solutions__slide-icon"><?= $card['icon_svg'] ?></div>
                                    <div class="solutions__slide-descr">
                                        <div class="solutions__slide-inner">
                                            <div class="solutions__slide-text"><?= e($card['description']) ?></div>
                                            <a href="<?= e($card['btn_url'] ?? '#') ?>" class="solutions__slide-btn button button--third"><?= e($card['btn_text'] ?? 'Learn more') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="solutions__slider swiper"><div class="solutions__wrapper swiper-wrapper">
                        <div class="solutions__slide swiper-slide"><div class="solutions__slide-body"><div class="solutions__slide-title">Add <?= ucfirst($tabName) ?> in Admin Panel</div><div class="solutions__slide-descr"><div class="solutions__slide-inner"><div class="solutions__slide-text">Go to Admin → Solutions to add items.</div></div></div></div></div>
                    </div></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ PROJECTS ═════════════════════════════════════════════ */ ?>
<?php if ($bk === 'projects'): ?>
<section class="projects">
    <div class="projects__container">
        <h2 class="projects__title title title--h1"><?= nl2br(e($h['projects_title'] ?? "Implemented\nWorkflows")) ?></h2>
        <div class="projects__items">
            <?php if (!empty($featured_cases)): ?>
            <?php foreach ($featured_cases as $case): ?>
            <a href="/cases/<?= e($case['slug']) ?>/" class="projects-card" data-fls-watcher="" data-fls-watcher-threshold="0.5">
                <div class="projects-card__body">
                    <div class="projects-card__head">
                        <?php if (!empty($case['company_name'])): ?>
                        <div class="projects-card__category"><?= e($case['company_name']) ?></div>
                        <?php endif; ?>
                        <div class="projects-card__title"><?= e($case['title'] ?? '') ?></div>
                    </div>
                    <div class="projects-card__descr">
                        <div class="projects-card__descr-inner">
                            <div class="projects-card__text"><?= e($case['description'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
                <div class="projects-card__img">
                    <?php if (!empty($case['image_path'])): ?>
                    <img alt="<?= e($case['title']) ?>" loading="lazy" src="<?= e(media_url($case['image_path'])) ?>">
                    <?php endif; ?>
                    <div class="projects-card__btn button button--icon button--border button--border-white">
                        <span class="button__text">More</span>
                        <span class="button__icon"><svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.8L12.5664 0.800049M12.5664 0.800049L12.5664 12.8M12.5664 0.800049L0.679613 0.800049" stroke="currentColor" stroke-width="1.6"></path></svg></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="<?= e($h['projects_btn_url'] ?? '/cases/') ?>" class="projects__link link">
            <span class="link__text"><?= e($h['projects_btn_text'] ?? 'View All Projects') ?></span>
            <span class="link__icon"><svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.7998L12.5664 0.799805M12.5664 0.799805L12.5664 12.7998M12.5664 0.799805L0.679613 0.799805" stroke="currentColor" stroke-width="1.6"></path></svg></span>
        </a>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ REVIEWS ══════════════════════════════════════════════ */ ?>
<?php if ($bk === 'reviews'): ?>
<section class="reviews">
    <div class="reviews__container">
        <div class="reviews__head">
            <h2 class="reviews__title title title--h1"><?= e($h['reviews_title'] ?? 'What clients say about us') ?></h2>
            <div class="reviews__navigation">
                <button type="button" class="swiper-button-prev"><svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M10.0527 0.523193L1.05273 8.52319L10.0527 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg></button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next"><svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg></button>
            </div>
        </div>
        <div data-fls-slider="" class="reviews__slider swiper">
            <div class="reviews__wrapper swiper-wrapper">
                <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $r): ?>
                <div class="reviews-card swiper-slide">
                    <div class="reviews-card__title"><?= e($r['quote']) ?></div>
                    <div class="reviews-card__text"><?= nl2br(e($r['text'])) ?></div>
                    <div class="reviews-card__user">
                        <div class="reviews-card__user-img">
                            <?php if (!empty($r['author_image_path'])): ?>
                            <img alt="<?= e($r['author_name']) ?>" loading="lazy" src="<?= e(media_url($r['author_image_path'])) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="reviews-card__user-body">
                            <div class="reviews-card__user-head">
                                <div class="reviews-card__user-name"><?= e($r['author_name']) ?></div>
                                <div class="reviews-card__user-work"><?= e($r['author_title']) ?></div>
                            </div>
                            <div class="reviews-card__user-socials">
                                <?php if (!empty($r['linkedin_url'])): ?>
                                <a href="<?= e($r['linkedin_url']) ?>" class="reviews-card__user-social" target="_blank">
                                    <img src="./assets/img/icons/linkedin.svg" alt="LinkedIn" loading="lazy">
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($r['rating_image_path'])): ?>
                                <a href="#" class="reviews-card__user-social">
                                    <img src="<?= e(media_url($r['rating_image_path'])) ?>" alt="Rating" loading="lazy">
                                </a>
                                <?php else: ?>
                                <a href="#" class="reviews-card__user-social">
                                    <img src="./assets/img/header/Rating%20Container.svg" alt="Rating" loading="lazy">
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="reviews-card swiper-slide">
                    <div class="reviews-card__title">Add reviews via the admin panel</div>
                    <div class="reviews-card__text">Go to Admin → Home Page → Reviews to add client testimonials.</div>
                    <div class="reviews-card__user"><div class="reviews-card__user-body"><div class="reviews-card__user-head"><div class="reviews-card__user-name">Admin</div><div class="reviews-card__user-work">SIDIS CMS</div></div></div></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ ROADMAP ═════════════════════════════════════════════ */ ?>
<?php if ($bk === 'roadmap'): ?>
<section class="roadmap">
    <div class="roadmap__container">
        <div class="roadmap__body">
            <div class="roadmap__head">
                <h2 class="roadmap__title title"><?= e($h['roadmap_title'] ?? 'Roadmap') ?></h2>
                <div class="roadmap__btns">
                    <a href="<?= e($h['roadmap_btn1_url'] ?? '#') ?>" class="roadmap__btn button"><?= e($h['roadmap_btn1_text'] ?? 'Get PDF') ?></a>
                    <a href="<?= e($h['roadmap_btn2_url'] ?? '#') ?>" class="roadmap__btn button button--icon">
                        <span class="button__text"><?= e($h['roadmap_btn2_text'] ?? 'Free audit') ?></span>
                        <span class="button__icon"><svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.707031 13L12.707 1M12.707 1L0.707031 1M12.707 1V12.8868" stroke="currentColor" stroke-width="2"></path></svg></span>
                    </a>
                </div>
            </div>
            <div class="roadmap__items">
                <?php
                $roadmap_steps = [
                    ['title'=>'Discovery Call','text'=>'Initial consultation. Project assessment. Solution overview.'],
                    ['title'=>'Strategy & Proposal','text'=>'Refine project scope. Define clear goals. Deliver tailored solutions.'],
                    ['title'=>'Integration','text'=>'Optimize workflows. Enhance collaboration. Accelerate growth.'],
                    ['title'=>'Support, Monitor & Scale','text'=>'Maximize uptime. Ensure peak performance. Drive continuous improvement.'],
                ];
                // Try to get from DB if roadmap_steps is stored
                if (!empty($h['roadmap_steps'])) {
                    $db_steps = json_decode($h['roadmap_steps'], true);
                    if ($db_steps) $roadmap_steps = $db_steps;
                }
                foreach ($roadmap_steps as $step):
                ?>
                <div class="roadmap__item">
                    <div class="roadmap__item-body">
                        <div class="roadmap__item-title"><?= e($step['title']) ?></div>
                        <div class="roadmap__item-text"><?= e($step['text']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="roadmap__bg" data-video-autoplay="">
        <video muted loop playsinline>
            <source src="./assets/video/1-hero.mp4" type="video/mp4">
        </video>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ GET IN TOUCH ════════════════════════════════════════ */ ?>
<?php if ($bk === 'getintouch'): ?>
<section id="getintouch" class="getintouch">
    <div class="getintouch__container">
        <div class="getintouch__body">
            <div class="getintouch__head">
                <h2 class="getintouch__title title title--h1"><?= e($h['cta_title'] ?? 'GET IN TOUCH') ?></h2>
                <div class="getintouch__items">
                    <div class="getintouch__item"><?= nl2br(e($h['cta_text'] ?? 'Fill out our contact form for a free consultation, or book an online meeting directly.')) ?></div>
                    <div class="getintouch__item"><?= e($h['cta_item2'] ?? 'We discuss your project even if you have just a raw idea.') ?></div>
                    <div class="getintouch__item"><?= e($h['cta_item3'] ?? 'We choose a model and approach suitable for your case and budget.') ?></div>
                </div>
            </div>
            <form class="getintouch__form" data-fls-form="dev" method="POST" action="/sendmail">
                <div class="getintouch__form-title"><?= e($h['cta_form_title'] ?? 'Request a free Consultation') ?></div>
                <div class="input-field">
                    <input required type="text" name="form[name]" data-fls-form-errtext="Error" placeholder="Full name" class="input-field__input input">
                </div>
                <div class="input-field">
                    <input required type="email" name="form[email]" data-fls-form-errtext="Error" placeholder="Email" class="input-field__input input">
                </div>
                <div class="getintouch__form-bottom">
                    <button type="submit" class="getintouch__send button button--third button--dark-hover">
                        <span><?= e($h['cta_btn_text'] ?? 'Send') ?></span>
                        <svg width="20" height="20" viewbox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.3072 5.69288L7.7071 10.2753L0.803747 7.97383C0.321882 7.81288-0.00275487 7.36078 1.76209e-05 6.85287C0.00282659 6.34496 0.331184 5.89563 0.81491 5.7403L18.4644 0.0565164C18.8839-0.0783506 19.3444 0.0323301 19.6561 0.34398C19.9677 0.655629 20.0784 1.11608 19.9435 1.53564L14.2597 19.1851C14.1044 19.6688 13.6551 19.9972 13.1472 20C12.6392 20.0028 12.1871 19.6781 12.0262 19.1963L9.71361 12.2595L14.3072 5.69288Z" fill="currentColor"></path></svg>
                    </button>
                    <div class="getintouch__form-info">We'll contact you shortly</div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ PRESENTATION ════════════════════════════════════════ */ ?>
<?php if ($bk === 'presentation'): ?>
<section class="presentation">
    <div class="presentation__container">
        <div class="presentation__body video-paused" data-video-controlls-parent="">
            <div class="presentation__head">
                <div class="presentation__subtitle"><?= e($h['presentation_subtitle'] ?? 'Bring Ideas To Life') ?></div>
                <h2 class="presentation__title title"><?= nl2br(e($h['presentation_title'] ?? "See RPA\nin Action")) ?></h2>
                <button type="button" class="presentation__play button button--icon" data-video-controlls-play="">
                    <span class="button__text">Play</span>
                    <span class="button__icon"><svg width="39" height="39" viewbox="0 0 39 39" fill="none"><rect width="38.8889" height="38.8889" rx="8.88889" fill="none"></rect><path d="M29.9993 18.4826C30.7401 18.9103 30.7401 19.9795 29.9993 20.4071L14.9994 29.0674C14.2586 29.4951 13.3327 28.9605 13.3327 28.1051L13.3327 10.7846C13.3327 9.92929 14.2586 9.39471 14.9994 9.82237L29.9993 18.4826Z" fill="currentColor"></path></svg></span>
                </button>
            </div>
            <div class="presentation__descr"><?= nl2br(e($h['presentation_text'] ?? '')) ?></div>
            <div class="presentation__video">
                <div class="video-controlls" data-video-controlls="">
                    <video loop playsinline muted preload="metadata">
                        <source src="<?= e($h['presentation_video'] ?? './assets/video/1-hero.mp4') ?>" type="video/mp4">
                    </video>
                    <div class="video-controlls__navigation">
                        <button class="video-controlls__mute" data-video-controlls-mute=""><svg width="36" height="36" viewbox="0 0 36 36" fill="none"><path d="M17.5534 14.3003L14.3169 11.0493L15.9188 9.44737C16.2144 9.14542 16.5606 9.07513 16.9578 9.23632C17.3549 9.39751 17.5534 9.68839 17.5534 10.1089V14.3003Z" fill="currentColor"></path></svg></button>
                        <input type="range" class="video-controlls__progress" data-video-controlls-progress="">
                        <button class="video-controlls__fullscreen" data-video-controlls-full=""><svg width="36" height="36" viewbox="0 0 36 36" fill="none"><path d="M8.67308 28.5C8.3407 28.5 8.06209 28.3876 7.83724 28.1627C7.61241 27.9379 7.5 27.6593 7.5 27.3269V22.0692C7.5 21.8023 7.59079 21.5784 7.77237 21.3979C7.95394 21.2173 8.17892 21.127 8.44732 21.127C8.71575 21.127 8.93906 21.2173 9.11727 21.3979C9.29547 21.5784 9.38457 21.8023 9.38457 22.0692V26.6155H13.9308C14.1978 26.6155 14.4216 26.7063 14.6021 26.8878C14.7828 27.0694 14.873 27.2944 14.873 27.5628C14.873 27.8312 14.7828 28.0545 14.6021 28.2327C14.4216 28.4109 14.1978 28.5 13.9308 28.5H8.67308Z" fill="currentColor"></path></svg></button>
                    </div>
                </div>
            </div>
            <div class="presentation__bg"><img alt="" loading="lazy" src="./assets/img/poster.webp"></div>
        </div>
    </div>
</section>
<?php endif; ?>


<?php /* ═══════ FAQ ══════════════════════════════════════════════════════ */ ?>
<?php if ($bk === 'faq'): ?>
<section class="faq">
    <div class="faq__container">
        <h2 class="faq__title title title--h1">Questions & answers</h2>
        <div data-fls-spollers="" data-fls-spollers-one="" class="faq__items spollers">
            <div class="spollers__column">
                <details class="spollers__item">
                    <summary class="spollers__title">What are your pricing options?</summary>
                    <div class="spollers__body">Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We deliberately maintain a compact team of professionals instead of a bloated staff. Over the years, we've built automated processes and refined systems which eliminates unnecessary work hours. We don't spend budget on expensive offices in central London or New York — we save that money for the client.</div>
                </details>
                <details class="spollers__item">
                    <summary class="spollers__title">How do you handle customer feedback?</summary>
                    <div class="spollers__body">Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We deliberately maintain a compact team of professionals instead of a bloated staff. Over the years, we've built automated processes and refined systems which eliminates unnecessary work hours. We don't spend budget on expensive offices in central London or New York — we save that money for the client.</div>
                </details>
                <details class="spollers__item">
                    <summary class="spollers__title">What is your typical project timeline?</summary>
                    <div class="spollers__body">Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We deliberately maintain a compact team of professionals instead of a bloated staff. Over the years, we've built automated processes and refined systems which eliminates unnecessary work hours. We don't spend budget on expensive offices in central London or New York — we save that money for the client.</div>
                </details>
            </div>
            <div class="spollers__column">
                <details class="spollers__item">
                    <summary class="spollers__title">Can you provide case studies or testimonials?</summary>
                    <div class="spollers__body">Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We deliberately maintain a compact team of professionals instead of a bloated staff. Over the years, we've built automated processes and refined systems which eliminates unnecessary work hours. We don't spend budget on expensive offices in central London or New York — we save that money for the client.</div>
                </details>
                <details class="spollers__item">
                    <summary class="spollers__title">What sets you apart from competitors?</summary>
                    <div class="spollers__body">Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We deliberately maintain a compact team of professionals instead of a bloated staff. Over the years, we've built automated processes and refined systems which eliminates unnecessary work hours. We don't spend budget on expensive offices in central London or New York — we save that money for the client.</div>
                </details>
                <details class="spollers__item">
                    <summary class="spollers__title">How do you ensure quality in your work?</summary>
                    <div class="spollers__body">Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We deliberately maintain a compact team of professionals instead of a bloated staff. Over the years, we've built automated processes and refined systems which eliminates unnecessary work hours. We don't spend budget on expensive offices in central London or New York — we save that money for the client.</div>
                </details>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* ═══════ LATEST ARTICLES ═════════════════════════════════════════ */ ?>
<?php if ($bk === 'articles' && !empty($recent_posts)): ?>
<section class="articles">
    <div class="articles__container">
        <div class="articles__head">
            <h2 class="articles__title title title--h1">
                Latest Automation<br>Insights
            </h2>
            <div class="articles__navigation">
                <button type="button" class="swiper-button-prev">
                    <svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.0527 0.523193L1.05273 8.52319L10.0527 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>
                </button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next">
                    <svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>
                </button>
            </div>
        </div>
        <div data-fls-slider="" class="articles__slider swiper">
            <div class="articles__wrapper swiper-wrapper">
                <?php $blog_fallbacks = ['/assets/img/blog/image-4.webp','/assets/img/blog/image-3.webp','/assets/img/blog/image-2.webp','/assets/img/blog/image-1.jpg']; ?>
                <?php foreach ($recent_posts as $pi => $post): ?>
                <article class="article-card swiper-slide" data-fls-watcher="" data-fls-watcher-threshold="0.6">
                    <div class="article-card__img">
                        <img alt="<?= e($post['title'] ?? '') ?>" loading="lazy" src="<?= !empty($post['image_path']) ? e(media_url($post['image_path'])) : $blog_fallbacks[$pi % 4] ?>">
                        <a href="/blog/<?= e($post['slug'] ?? '') ?>" class="article-card__arrow button button--icon">
                            <span class="button__text">Open article</span>
                            <span class="button__icon">
                                <svg width="14" height="14" viewbox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.707031 13L12.707 1M12.707 1L0.707031 1M12.707 1V12.8868" stroke="currentColor" stroke-width="2"></path></svg>
                            </span>
                        </a>
                    </div>
                    <a href="/blog/<?= e($post['slug'] ?? '') ?>" class="article-card__title"><?= e($post['title'] ?? '') ?></a>
                    <div class="article-card__badges">
                        <?php if (!empty($post['published_at'])): ?>
                        <div class="article-card__badge"><?= date('j, F, Y', strtotime($post['published_at'])) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($post['author_name'])): ?>
                    <div class="article-card__author">
                        <?php if (!empty($post['author_image_path'])): ?>
                        <div class="article-card__author-img">
                            <img alt="<?= e($post['author_name']) ?>" loading="lazy" src="<?= e(media_url($post['author_image_path'])) ?>">
                        </div>
                        <?php endif; ?>
                        <div class="article-card__author-body">
                            <div class="article-card__author-name"><?= e($post['author_name']) ?></div>
                            <?php if (!empty($post['author_title'])): ?>
                            <div class="article-card__author-position"><?= e($post['author_title']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php endforeach; ?>
</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
