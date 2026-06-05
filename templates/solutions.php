<?php
require __DIR__ . '/layout.php';

$sp     = $solution_page ?? [];
$blocks = $sol_page_blocks ?? [];
$h      = $home ?? [];

// Blocks are lazy-initialized in get_sol_page_blocks() with full placeholder content

$arrow_svg = '<svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.8L12.5664 0.799988M12.5664 0.799988L0.566406 0.799988M12.5664 0.799988V12.6868" stroke="currentColor" stroke-width="1.6"/></svg>';
$nav_prev  = '<svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M10.0527 0.523193L1.05273 8.52319L10.0527 16.5232" stroke="currentColor" stroke-width="1.4"/></svg>';
$nav_next  = '<svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"/></svg>';

// Convert ./relative paths to /absolute for nested page URLs
function sol_url(string $path): string {
    if (!$path) return '';
    if (strpos($path, './') === 0) return '/' . ltrim($path, './');
    return media_url($path);
}
?>
<main class="page">

<?php foreach ($blocks as $block):
    $bk = $block['block_key'];
    $c  = $block['content'] ?? [];
?>

<?php /* ═══ PROMO ══════════════════════════════════════════════════════════ */ ?>
<?php if ($bk === 'promo'): ?>
<section class="promo-category">
    <div class="promo-category__container">
        <div class="promo-category__body">
            <div class="promo-category__content">
                <h1 class="promo-category__title title title--h2"><?= e($c['title'] ?: ($sp['title'] ?? '')) ?></h1>
                <?php $promo_text = $c['text'] ?: ($sp['description'] ?? ''); ?>
                <?php if (!empty($promo_text)): ?>
                <div class="promo-category__text"><?= nl2br(e($promo_text)) ?></div>
                <?php endif; ?>
                <div class="promo-category__btns">
                    <?php $btn1 = ($c['btn1_text'] ?: ($sp['btn1_text'] ?? '')) ?: 'View Our Presentation'; ?>
                    <?php $btn1url = $c['btn1_url'] ?: '#'; ?>
                    <a href="<?= e($btn1url) ?>" target="_blank" class="promo-category__btn button"><?= e($btn1) ?></a>
                    <?php $btn2 = ($c['btn2_text'] ?: ($sp['btn2_text'] ?? '')) ?: 'Free audit'; ?>
                    <?php $btn2url = $c['btn2_url'] ?: '#getintouch'; ?>
                    <a href="<?= e($btn2url) ?>" class="promo-category__btn button button--icon">
                        <span class="button__text"><?= e($btn2) ?></span>
                        <span class="button__icon"><?= $arrow_svg ?></span>
                    </a>
                </div>
            </div>
            <div class="promo-category__image">
                <?php $img = media_url($c['image_url'] ?: ($sp['image_path'] ?: '/assets/img/promo/image-1.webp')); ?>
                <img alt="<?= e($sp['title'] ?? '') ?>" loading="lazy" src="<?= e($img) ?>">
                <?php $clutch_url = get_setting('clutch_url') ?: '#'; ?>
                <div class="promo-category__rating">
                    <div class="promo-category__rating-body">
                        <a href="<?= e($clutch_url) ?>" class="promo-category__rating-item" target="_blank" rel="noopener"><img src="/assets/img/header/Rating%20Container.svg" width="92" height="44" alt="Clutch 5.0/5.0" loading="lazy"></a>
                        <a href="<?= e($clutch_url) ?>" class="promo-category__rating-item" target="_blank" rel="noopener"><img src="/assets/img/header/Top%20Rated%20Badge.png" width="170" height="44" alt="Upwork Top Rated" loading="lazy"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* ═══ INNER WRAPPER (aside nav + blocks) ════════════════════════════ */ ?>
<?php if ($bk === 'features'): ?>
<?php if (!$features_wrapper_open ?? true): $features_wrapper_open = true; ?>
<div class="inner">
    <aside class="inner__aside aside-nav" data-article-menu="" data-fls-header-scroll="" data-fls-header-scroll-show="">
        <div class="aside-nav__body">
            <ul class="aside-nav__list">
                <?php foreach ($blocks as $nb):
                    $nk = $nb['block_key'];
                    $nc = $nb['content'] ?? [];
                    $anchor_map = ['features'=>'some-section-1','planning'=>'some-section-2','solved'=>'some-section-3','roadmap'=>'some-section-4','projects'=>'some-section-5','reviews'=>'some-section-6'];
                    $label_map  = ['features'=>'What we offer','planning'=>'What we do','solved'=>'Our solutions','roadmap'=>'Our process','projects'=>'Case studies','reviews'=>'Customer Reviews'];
                    if (!isset($anchor_map[$nk]) || !$nb['is_active']) continue;
                ?>
                <li class="aside-nav__item">
                    <a href="#" class="aside-nav__link" data-fls-scrollto-header="" data-fls-scrollto="#<?= $anchor_map[$nk] ?>"><?= e($label_map[$nk]) ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="aside-nav__demo">
                <img alt="Image" loading="lazy" src="/assets/img/projects/image-5.webp">
                <a href="#getintouch" class="aside-nav__demo-btn button button--icon button--border button--border-white">
                    <span class="button__text">Demo</span>
                    <span class="button__icon"><?= $arrow_svg ?></span>
                </a>
            </div>
        </div>
        <div class="aside-nav__bottom">
            <button type="button" class="aside-nav__burger" data-article-menu-btn=""><span></span><span></span><span></span><span></span></button>
            <a href="#getintouch" class="aside-nav__btn button button--third button--dark-hover">Contact us</a>
        </div>
    </aside>
<?php endif; ?>

<section id="some-section-1" class="features" data-scroll-section="">
    <div class="features__container">
        <h2 class="features__title title title--h1"><?= e($c['title'] ?? 'What you get') ?></h2>
        <div data-fls-slider="" class="features__slider swiper">
            <div class="features__wrapper swiper-wrapper">
                <?php foreach ($c['slides'] ?? [] as $slide): ?>
                <div class="features__slide swiper-slide">
                    <div class="features__slide-title"><?= e($slide['title'] ?? '') ?></div>
                    <div class="features__slide-text"><?= e($slide['text'] ?? '') ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="features__navigation">
                <button type="button" class="swiper-button-prev"><?= $nav_prev ?></button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next"><?= $nav_next ?></button>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($bk === 'planning'): ?>
<section id="some-section-2" class="planning" data-scroll-section="">
    <div class="planning__container">
        <div class="planning__body">
            <div data-fls-slider="" class="planning__descr swiper">
                <div class="planning__wrapper swiper-wrapper">
                    <?php foreach ($c['items'] ?? [] as $item): ?>
                    <div class="planning__item swiper-slide">
                        <h3 class="planning__item-title title title--h2"><?= e($item['title'] ?? '') ?></h3>
                        <div class="planning__item-text"><?= nl2br(e($item['text'] ?? '')) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div data-fls-dynamic=".planning__navigation, 992" class="swiper-pagination"></div>
            </div>
            <div class="planning__images">
                <div data-fls-slider="" class="planning__slider swiper">
                    <div class="planning__wrapper swiper-wrapper">
                        <?php if (!empty($c['images'])): ?>
                        <?php foreach ($c['images'] as $img): ?>
                        <div class="planning__slide swiper-slide">
                            <img alt="Image" loading="lazy" src="<?= e(sol_url($img['image_url'] ?? '')) ?>">
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="planning__slide swiper-slide">
                            <img alt="Image" loading="lazy" src="/assets/img/automation/image.webp">
                        </div>
                        <div class="planning__slide swiper-slide">
                            <img alt="Image" loading="lazy" src="/assets/img/promo/image-1.webp">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div data-fls-dynamic=".planning__body, 992" class="planning__navigation">
                    <button type="button" class="swiper-button-prev"><?= $nav_prev ?></button>
                    <button type="button" class="swiper-button-next"><?= $nav_next ?></button>
                </div>
            </div>
        </div>
        <div data-fls-marquee="" data-fls-marquee-space="60" data-fls-marquee-speed="765" class="planning__marquee">
            <?php if (!empty($partners)): ?>
            <?php foreach ($partners as $logo): ?>
            <div class="planning__marquee-item"><img alt="<?= e($logo['alt_text']) ?>" loading="lazy" src="<?= e(media_url($logo['image_path'])) ?>"></div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="planning__marquee-item"><img alt="Partner" loading="lazy" src="/assets/img/automation/partner-1.webp"></div>
            <div class="planning__marquee-item"><img alt="Partner" loading="lazy" src="/assets/img/automation/partner-2.webp"></div>
            <div class="planning__marquee-item"><img alt="Partner" loading="lazy" src="/assets/img/automation/partner-3.webp"></div>
            <div class="planning__marquee-item"><img alt="Partner" loading="lazy" src="/assets/img/automation/partner-4.webp"></div>
            <?php endif; ?>
        </div>
        <?php if (!empty($c['info_title'])): ?>
        <div class="planning__info">
            <div class="planning__info-img">
                <img alt="Image" loading="lazy" src="/assets/img/projects/image-6.webp">
            </div>
            <div class="planning__info-body">
                <div class="planning__info-title"><?= e($c['info_title']) ?></div>
                <div class="planning__info-btns">
                    <?php if (!empty($c['info_btn1_text'])): ?>
                    <a href="<?= e($c['info_btn1_url'] ?? '/assets/Sidis-Group.pdf') ?>" target="_blank" class="planning__info-btn button"><?= e($c['info_btn1_text']) ?></a>
                    <?php endif; ?>
                    <?php if (!empty($c['info_btn2_text'])): ?>
                    <a href="<?= e($c['info_btn2_url'] ?? '#getintouch') ?>" class="planning__info-btn button button--icon">
                        <span class="button__text"><?= e($c['info_btn2_text']) ?></span>
                        <span class="button__icon"><?= $arrow_svg ?></span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($bk === 'solved'): ?>
<section id="some-section-3" class="solved" data-scroll-section="">
    <div class="solved__container">
        <div class="solved__head">
            <h2 class="solved__title title title--h1"><?= e($c['title'] ?? 'Business Challenges Solved') ?></h2>
            <div class="solved__navigation">
                <button type="button" class="swiper-button-prev"><?= $nav_prev ?></button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next"><?= $nav_next ?></button>
            </div>
        </div>
        <div data-fls-slider="" class="solved__slider swiper">
            <div class="solved__wrapper swiper-wrapper">
                <?php foreach ($c['slides'] ?? [] as $slide): ?>
                <div class="solved__slide swiper-slide">
                    <div class="solved__slide-title"><?= nl2br(e($slide['title'] ?? '')) ?></div>
                    <div class="solved__slide-text"><?= nl2br(e($slide['text'] ?? '')) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($bk === 'roadmap'): ?>
<section id="some-section-4" class="roadmap" data-scroll-section="">
    <div class="roadmap__container">
        <div class="roadmap__body">
            <div class="roadmap__head">
                <h2 class="roadmap__title title title--h1"><?= e($c['title'] ?? 'Our Process') ?></h2>
                <div class="roadmap__btns">
                    <a href="<?= e($c['btn1_url'] ?? '#') ?>" class="roadmap__btn button"><?= e($c['btn1_text'] ?? 'Get PDF') ?></a>
                    <a href="<?= e($c['btn2_url'] ?? '#getintouch') ?>" class="roadmap__btn button button--icon">
                        <span class="button__text"><?= e($c['btn2_text'] ?? 'Free audit') ?></span>
                        <span class="button__icon"><?= $arrow_svg ?></span>
                    </a>
                </div>
            </div>
            <div class="roadmap__items">
                <?php foreach ($c['steps'] ?? [] as $step): ?>
                <div class="roadmap__item">
                    <div class="roadmap__item-body">
                        <div class="roadmap__item-title"><?= e($step['title'] ?? '') ?></div>
                        <div class="roadmap__item-text"><?= e($step['text'] ?? '') ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="roadmap__bg" data-video-autoplay="">
        <video muted loop playsinline preload="none"<?= !empty($c['video_poster']) ? ' poster="' . e(sol_url($c['video_poster'])) . '"' : '' ?>>
            <source src="<?= e(sol_url($c['video_path'] ?? './assets/video/1-hero.mp4')) ?>" type="video/mp4">
        </video>
    </div>
</section>
<?php endif; ?>

<?php
// Filter cases by selected_ids if specified
$block_cases = $featured_cases ?? [];
if (!empty($c['selected_ids']) && is_array($c['selected_ids'])) {
    $sel = array_map('intval', $c['selected_ids']);
    $block_cases = array_filter($block_cases ?? [], fn($x) => in_array((int)$x['id'], $sel));
    if (empty($block_cases) && !empty($sel)) {
        $block_cases = rows('SELECT c.*, ct.title, ct.description, img.path as image_path, c.company
                             FROM cases c
                             LEFT JOIN cases_t ct ON c.id=ct.case_id AND ct.lang_id=' . $lang_id . '
                             LEFT JOIN media img ON c.featured_image_id=img.id
                             WHERE c.id IN (' . implode(',', $sel) . ') AND c.is_active=1
                             ORDER BY FIELD(c.id,' . implode(',', $sel) . ')');
        foreach ($block_cases as &$bc) {
            $bc['terms'] = rows('SELECT ct.type, tt.name FROM case_terms ct JOIN terms_t tt ON ct.term_id=tt.term_id AND tt.lang_id=? WHERE ct.case_id=?', [$lang_id, $bc['id']]);
        }
        unset($bc);
    }
}
?>
<?php if ($bk === 'projects' && !empty($block_cases)): ?>
<section id="some-section-5" class="projects" data-scroll-section="">
    <div class="projects__container">
        <h2 class="projects__title title title--h1"><?= nl2br(e($c['title'] ?? "Implemented\nWorkflows")) ?></h2>
        <div class="projects__items">
            <?php $_ext_btn = get_setting('case_external_btn_text') ?: 'Visit Website'; ?>
            <?php foreach (array_slice(array_values($block_cases), 0, 4) as $case): ?>
            <?php $cl = case_link($case); ?>
            <?php if ($cl['href']): ?><a href="<?= e($cl['href']) ?>"<?= $cl['external'] ? ' target="_blank" rel="noopener noreferrer"' : '' ?> class="projects-card" data-fls-watcher="" data-fls-watcher-threshold="0.5"><?php else: ?><div class="projects-card" data-fls-watcher="" data-fls-watcher-threshold="0.5"><?php endif; ?>
                <div class="projects-card__body">
                    <div class="projects-card__head">
                        <?php if (!empty($case['company_name'])): ?>
                        <div class="projects-card__category"><?= e($case['company_name']) ?></div>
                        <?php endif; ?>
                        <div class="projects-card__title"><?= e($case['title'] ?? '') ?></div>
                    </div>
                    <div class="projects-card__descr">
                        <?php if (!empty($case['terms'])): ?>
                        <div class="projects-card__tags">
                            <?php foreach (array_slice($case['terms'], 0, 3) as $term): ?>
                            <div class="projects-card__tag"><?= e($term['name']) ?></div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <div class="projects-card__descr-inner">
                            <div class="projects-card__text"><?= e($case['description'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
                <div class="projects-card__img">
                    <?php if (!empty($case['image_path'])): ?>
                    <img alt="<?= e($case['title'] ?? '') ?>" loading="lazy" src="<?= e(sol_url($case['image_path'] ?? '')) ?>">
                    <?php endif; ?>
                    <?php if ($cl['href']): ?>
                    <div class="projects-card__btn button button--icon button--border button--border-white">
                        <span class="button__text"><?= e($cl['external'] ? $_ext_btn : 'More') ?></span>
                        <span class="button__icon"><svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.8L12.5664 0.800049M12.5664 0.800049L12.5664 12.8M12.5664 0.800049L0.679613 0.800049" stroke="currentColor" stroke-width="1.6"></path></svg></span>
                    </div>
                    <?php endif; ?>
                </div>
            <?php if ($cl['href']): ?></a><?php else: ?></div><?php endif; ?>
            <?php endforeach; ?>
        </div>
        <a href="/cases/" class="projects__link link">
            <span class="link__text">View All Projects</span>
            <span class="link__icon"><svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.7998L12.5664 0.799805M12.5664 0.799805L12.5664 12.7998M12.5664 0.799805L0.679613 0.799805" stroke="currentColor" stroke-width="1.6"/></svg></span>
        </a>
    </div>
</section>
<?php endif; ?>

<?php
// Filter reviews by selected_ids if specified
$block_reviews = $reviews ?? [];
if (!empty($c['selected_ids']) && is_array($c['selected_ids'])) {
    $sel = array_map('intval', $c['selected_ids']);
    $block_reviews = array_filter($reviews ?? [], fn($r) => in_array((int)$r['id'], $sel));
}
?>
<?php if ($bk === 'reviews' && !empty($block_reviews)): ?>
<section id="some-section-6" class="reviews" data-scroll-section="">
    <div class="reviews__container">
        <div class="reviews__head">
            <h2 class="reviews__title title title--h1"><?= e($c['title'] ?? 'What clients say about us') ?></h2>
            <div class="reviews__navigation">
                <button type="button" class="swiper-button-prev"><?= $nav_prev ?></button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next"><?= $nav_next ?></button>
            </div>
        </div>
        <div data-fls-slider="" class="reviews__slider swiper">
            <div class="reviews__wrapper swiper-wrapper">
                <?php foreach ($block_reviews as $r): ?>
                <div class="reviews-card swiper-slide">
                    <div class="reviews-card__title"><?= e($r['quote'] ?? '') ?></div>
                    <div class="reviews-card__text"><?= nl2br(e($r['text'] ?? '')) ?></div>
                    <div class="reviews-card__user">
                        <?php if (!empty($r['author_image_path'])): ?>
                        <div class="reviews-card__user-img"><img alt="<?= e($r['author_name'] ?? '') ?>" loading="lazy" src="<?= e(sol_url($r['author_image_path'] ?? '')) ?>"></div>
                        <?php endif; ?>
                        <div class="reviews-card__user-body">
                            <div class="reviews-card__user-head">
                                <div class="reviews-card__user-name"><?= e($r['author_name'] ?? '') ?></div>
                                <div class="reviews-card__user-work"><?= e($r['author_title'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* close inner wrapper after reviews */ ?>
<?php if ($bk === 'reviews'): ?></div><?php endif; ?>

<?php if ($bk === 'getintouch'): ?>
<section id="getintouch" class="getintouch">
    <div class="getintouch__container">
        <div class="getintouch__body">
            <div class="getintouch__head">
                <h2 class="getintouch__title title title--h1">GET IN TOUCH</h2>
                <div class="getintouch__items">
                    <div class="getintouch__item">Fill out our contact form for a free consultation, or book an online meeting directly via Google Meet.</div>
                    <div class="getintouch__item">We discuss your project even if you have just a raw idea.</div>
                    <div class="getintouch__item">We choose a model and approach that are suitable for your case and budget.</div>
                </div>
            </div>
            <form class="getintouch__form" data-fls-form="ajax" method="POST" action="/sendmail">
                <div class="getintouch__form-title">Request a free Consultation</div>
                <div class="input-field"><input required class="input-field__input input" type="text" name="form[name]" data-fls-form-errtext="Error" placeholder="Full name"></div>
                <div class="input-field"><input required class="input-field__input input" type="email" name="form[email]" data-fls-form-errtext="Error" placeholder="Email"></div>
                <div class="getintouch__form-bottom">
                    <button type="submit" class="getintouch__send button button--third button--dark-hover">
                        <span>Send</span>
                        <svg width="20" height="20" viewbox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.3072 5.69288L7.7071 10.2753L0.803747 7.97383C0.321882 7.81288 -0.00275487 7.36078 1.76209e-05 6.85287C0.00282659 6.34496 0.331184 5.89563 0.81491 5.7403L18.4644 0.0565164C18.8839 -0.0783506 19.3444 0.0323301 19.6561 0.34398C19.9677 0.655629 20.0784 1.11608 19.9435 1.53564L14.2597 19.1851C14.1044 19.6688 13.6551 19.9972 13.1472 20C12.6392 20.0028 12.1871 19.6781 12.0262 19.1963L9.71361 12.2595L14.3072 5.69288Z" fill="currentColor"></path></svg>
                    </button>
                    <div class="getintouch__form-info">We'll contact you shortly</div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($bk === 'faq'): ?>
<section class="faq">
    <div class="faq__container">
        <h2 class="faq__title title title--h1"><?= e($c['title'] ?? 'Questions & answers') ?></h2>
        <?php $faq_items = $c['items'] ?? []; $half = (int)ceil(count($faq_items)/2); ?>
        <div data-fls-spollers="" data-fls-spollers-one="" class="faq__items spollers">
            <div class="spollers__column">
                <?php foreach (array_slice($faq_items, 0, $half) as $item): ?>
                <details class="spollers__item">
                    <summary class="spollers__title"><?= e($item['q'] ?? '') ?></summary>
                    <div class="spollers__body"><?= nl2br(e($item['a'] ?? '')) ?></div>
                </details>
                <?php endforeach; ?>
            </div>
            <div class="spollers__column">
                <?php foreach (array_slice($faq_items, $half) as $item): ?>
                <details class="spollers__item">
                    <summary class="spollers__title"><?= e($item['q'] ?? '') ?></summary>
                    <div class="spollers__body"><?= nl2br(e($item['a'] ?? '')) ?></div>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Filter posts by selected_ids if specified
$block_posts = $recent_posts ?? [];
if (!empty($c['selected_ids']) && is_array($c['selected_ids'])) {
    $sel = array_map('intval', $c['selected_ids']);
    $block_posts = array_filter($recent_posts ?? [], fn($p) => in_array((int)$p['id'], $sel));
}
?>
<?php if ($bk === 'articles' && !empty($block_posts)): ?>
<section class="articles">
    <div class="articles__container">
        <div class="articles__head">
            <h2 class="articles__title title title--h1"><?= nl2br(e($c['title'] ?? 'Latest Automation Insights')) ?></h2>
            <div class="articles__navigation">
                <button type="button" class="swiper-button-prev"><?= $nav_prev ?></button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next"><?= $nav_next ?></button>
            </div>
        </div>
        <div data-fls-slider="" class="articles__slider swiper">
            <div class="articles__wrapper swiper-wrapper">
                <?php $blog_fallbacks = ['/assets/img/blog/image-4.webp','/assets/img/blog/image-3.webp','/assets/img/blog/image-2.webp','/assets/img/blog/image-1.jpg']; ?>
                <?php foreach (array_values($block_posts) as $pi => $post): ?>
                <article class="article-card swiper-slide" data-fls-watcher="" data-fls-watcher-threshold="0.6">
                    <div class="article-card__img">
                        <img alt="<?= e($post['title'] ?? '') ?>" loading="lazy" src="<?= !empty($post['image_path']) ? e(sol_url($post['image_path'])) : $blog_fallbacks[$pi % 4] ?>">
                        <a href="/blog/<?= e($post['slug'] ?? '') ?>/" class="article-card__arrow button button--icon">
                            <span class="button__text">Open article</span>
                            <span class="button__icon"><svg width="14" height="14" viewbox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.707031 13L12.707 1M12.707 1L0.707031 1M12.707 1V12.8868" stroke="currentColor" stroke-width="2"/></svg></span>
                        </a>
                    </div>
                    <a href="/blog/<?= e($post['slug'] ?? '') ?>/" class="article-card__title"><?= e($post['title'] ?? '') ?></a>
                    <div class="article-card__badges">
                        <?php if (!empty($post['published_at'])): ?>
                        <div class="article-card__badge"><?= date('j, F, Y', strtotime($post['published_at'])) ?></div>
                        <?php endif; ?>
                        <div class="article-card__badge"><?= (int)($post['read_time'] ?? 1) ?> min to read</div>
                    </div>
                    <?php if (!empty($post['author_name'])): ?>
                    <div class="article-card__author">
                        <?php if (!empty($post['author_image_path'])): ?>
                        <div class="article-card__author-img">
                            <img alt="<?= e($post['author_name']) ?>" loading="lazy" src="<?= e(sol_url($post['author_image_path'])) ?>">
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

<?php
// Close inner wrapper if it was opened but reviews block was not present
if (!empty($features_wrapper_open)):
    $has_reviews = false;
    foreach ($blocks as $b) { if ($b['block_key'] === 'reviews' && $b['is_active']) { $has_reviews = true; break; } }
    if (!$has_reviews): ?></div><?php endif;
endif;
?>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
