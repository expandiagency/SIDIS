<?php require __DIR__ . '/layout.php';

$nav_prev = '<svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M10.0527 0.523193L1.05273 8.52319L10.0527 16.5232" stroke="currentColor" stroke-width="1.4"/></svg>';
$nav_next = '<svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"/></svg>';

$ex = $case['extras'] ?? [];

// Group terms by type
$termsByType = [];
foreach ($case['terms'] ?? [] as $term) {
    $termsByType[$term['type']][] = $term['name'];
}
?>
<main class="page">

<!-- ═══ PROMO ═══════════════════════════════════════════════════════════════ -->
<section class="promo-case">
    <div class="promo-case__container">
        <div class="promo-case__body">
            <div class="promo-case__content">
                <?php if (!empty($case['logo_path'])): ?>
                <div class="promo-case__logo">
                    <img alt="<?= e($case['company_name'] ?? '') ?>" loading="lazy" src="<?= e(media_url($case['logo_path'])) ?>">
                </div>
                <?php elseif (!empty($case['company_name'])): ?>
                <div class="promo-case__logo" style="font-size:18px;font-weight:600"><?= e($case['company_name']) ?></div>
                <?php endif; ?>
                <div class="promo-case__info">
                    <h1 class="promo-case__title title title--h2"><?= e($case['title'] ?? '') ?></h1>
                    <?php if (!empty($case['description'])): ?>
                    <div class="promo-case__text"><?= nl2br(e($case['description'])) ?></div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($case['key_results'])): ?>
                <div class="promo-case__result">
                    <div class="promo-case__label">Key results</div>
                    <ul class="promo-case__list">
                        <?php foreach ($case['key_results'] as $r):
                            $items = array_filter(array_map('trim', explode('·', $r['result_text'] ?? $r['text'] ?? '')));
                            foreach ($items as $item): ?>
                        <li class="promo-case__item"><?= e($item) ?></li>
                        <?php endforeach; endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <div class="promo-case__img" style="align-self:stretch">
                <picture style="display:block;width:100%;height:100%">
                    <source srcset="/assets/img/promo/image-3-mob.webp" media="(max-width: 650px)">
                    <?php if (!empty($case['image_path'])): ?>
                    <img alt="<?= e($case['title'] ?? '') ?>" loading="lazy" src="<?= e(media_url($case['image_path'])) ?>" style="width:100%;height:100%;object-fit:cover">
                    <?php else: ?>
                    <img alt="<?= e($case['title'] ?? '') ?>" loading="lazy" src="/assets/img/promo/image-4.webp" style="width:100%;height:100%;object-fit:cover">
                    <?php endif; ?>
                </picture>
            </div>
        </div>
    </div>
</section>

<!-- ═══ OVERVIEW ════════════════════════════════════════════════════════════ -->
<section class="overview">
    <div class="overview__container">
        <div class="overview__head">
            <h2 class="overview__title">Project Overview</h2>
            <?php if (!empty($case['overview_text'])): ?>
            <div class="overview__text" data-scroll-text="">
                <p class="overview__text-value" data-scroll-text-value=""><?= nl2br(e($case['overview_text'])) ?></p>
                <div class="overview__text-mask" data-scroll-text-mask=""></div>
            </div>
            <?php endif; ?>
        </div>
        <div class="overview__body">
            <div class="overview__col">
                <?php foreach ($termsByType as $type => $names): ?>
                <div class="overview__item">
                    <div class="overview__label"><?= e(ucfirst($type) . 's') ?></div>
                    <div class="overview__value"><?= e(implode(', ', $names)) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="overview__col">
                <?php if (!empty($case['location'])): ?>
                <div class="overview__item">
                    <div class="overview__label">Location</div>
                    <div class="overview__value"><?= e($case['location']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($case['cooperation_period'])): ?>
                <div class="overview__item">
                    <div class="overview__label">Cooperation period</div>
                    <div class="overview__value"><?= e($case['cooperation_period']) ?></div>
                </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($case['services'])): ?>
            <div class="overview__info">
                <div class="overview__label">Services used</div>
                <div class="overview__tags">
                    <?php foreach ($case['services'] as $s): ?>
                    <div class="overview__tag"><?= e($s['service_name'] ?? $s['name'] ?? '') ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ═══ CHALLENGES ══════════════════════════════════════════════════════════ -->
<?php if (!empty($case['challenges']) || !empty($ex['challenges'])): ?>
<?php $challenges = !empty($case['challenges']) ? $case['challenges'] : ($ex['challenges'] ?? []); ?>
<section class="advantages">
    <div class="advantages__container">
        <div class="advantages__body">
            <div class="advantages__head" data-fls-header-scroll="" data-fls-header-scroll-show="">
                <h2 class="advantages__title title title--h1">Challenges to Resolve</h2>
                <?php if (!empty($ex['challenges_text'])): ?>
                <div class="advantages__text"><?= nl2br(e($ex['challenges_text'])) ?></div>
                <?php endif; ?>
            </div>
            <div class="advantages__items">
                <?php foreach ($challenges as $ch): ?>
                <div class="advantages__item" data-fls-watcher="" data-fls-watcher-threshold="0.6">
                    <div class="advantages__item-title"><?= e($ch['title'] ?? '') ?></div>
                    <div class="advantages__item-text"><?= nl2br(e($ch['text'] ?? '')) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ TECH STACK ══════════════════════════════════════════════════════════ -->
<?php if (!empty($case['tech_items']) || !empty($ex['tech_items'])): ?>
<?php
// Prefer extras tech_items as they include descriptions; fall back to DB items
if (!empty($ex['tech_items'])) {
    $tech_items = $ex['tech_items'];
} else {
    $tech_items = $case['tech_items'] ?? [];
}
?>
<section class="tech">
    <div class="tech__container">
        <div class="tech__head">
            <div class="tech__info">
                <h2 class="tech__title title title--h1">Tech Stack Used</h2>
                <?php if (!empty($ex['tech_text'])): ?>
                <div class="tech__text"><?= nl2br(e($ex['tech_text'])) ?></div>
                <?php endif; ?>
            </div>
            <div class="tech__navigation">
                <button type="button" class="swiper-button-prev"><?= $nav_prev ?></button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next"><?= $nav_next ?></button>
            </div>
        </div>
        <div data-fls-slider="" class="tech__slider swiper">
            <div class="tech__wrapper swiper-wrapper">
                <?php foreach ($tech_items as $i => $tech): ?>
                <div class="tech__slide swiper-slide">
                    <div class="tech__slide-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></div>
                    <div class="tech__slide-body">
                        <div class="tech__slide-title"><?= nl2br(e($tech['name'] ?? '')) ?></div>
                        <?php if (!empty($tech['description'])): ?>
                        <div class="tech__slide-text"><?= nl2br(e($tech['description'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ THE SOLUTION (descrblock) ═══════════════════════════════════════════ -->
<?php if (!empty($ex['solution_title'])): ?>
<section class="descrblock">
    <div class="descrblock__container">
        <div class="descrblock__body">
            <div class="descrblock__content">
                <div class="descrblock__subtitle title title--h6"><?= e($ex['solution_subtitle'] ?? 'The Solution') ?></div>
                <h2 class="descrblock__title title title--h1"><?= e($ex['solution_title']) ?></h2>
                <div class="descrblock__texts">
                    <?php if (!empty($ex['solution_text1'])): ?>
                    <div class="descrblock__text"><?= $ex['solution_text1'] ?></div>
                    <?php endif; ?>
                    <?php if (!empty($ex['solution_text2'])): ?>
                    <div class="descrblock__text"><?= $ex['solution_text2'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($ex['solution_image'])): ?>
            <div class="descrblock__img">
                <picture>
                    <img alt="Solution" loading="lazy" src="<?= e($ex['solution_image']) ?>">
                </picture>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ FINAL RESULTS ═══════════════════════════════════════════════════════ -->
<?php if (!empty($ex['results'])): ?>
<section class="advantages">
    <div class="advantages__container">
        <div class="advantages__body">
            <div class="advantages__head" data-fls-header-scroll="" data-fls-header-scroll-show="">
                <h2 class="advantages__title title title--h1"><?= e($ex['results_title'] ?? 'Final Results') ?></h2>
                <?php if (!empty($ex['results_text'])): ?>
                <div class="advantages__text"><?= nl2br(e($ex['results_text'])) ?></div>
                <?php endif; ?>
            </div>
            <div class="advantages__items">
                <?php foreach ($ex['results'] as $r): ?>
                <div class="advantages__item" data-fls-watcher="" data-fls-watcher-threshold="0.6">
                    <div class="advantages__item-title"><?= e($r['title'] ?? '') ?></div>
                    <div class="advantages__item-text"><?= nl2br(e($r['text'] ?? '')) ?></div>
                </div>
                <?php endforeach; ?>
                <?php if (!empty($ex['results_image'])): ?>
                <div class="advantages__img">
                    <img alt="Results" loading="lazy" src="<?= e($ex['results_image']) ?>">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ TESTIMONIAL (result) ════════════════════════════════════════════════ -->
<?php if (!empty($ex['result_quote'])): ?>
<section class="result">
    <div class="result__container">
        <div class="result__body">
            <div class="result__text">"<?= e($ex['result_quote']) ?>"</div>
            <div class="result__user">
                <?php if (!empty($ex['result_user_image'])): ?>
                <div class="result__user-img">
                    <img alt="<?= e($ex['result_user_name'] ?? '') ?>" loading="lazy" src="<?= e($ex['result_user_image']) ?>">
                </div>
                <?php endif; ?>
                <div class="result__user-body">
                    <div class="result__user-head">
                        <div class="result__user-name"><?= e($ex['result_user_name'] ?? '') ?></div>
                        <div class="result__user-work"><?= e($ex['result_user_work'] ?? '') ?></div>
                    </div>
                    <div class="result__user-socials">
                        <?php if (!empty($ex['result_linkedin'])): ?>
                        <a href="<?= e($ex['result_linkedin']) ?>" class="result__user-social" target="_blank">
                            <img src="/assets/img/icons/linkedin.svg" alt="LinkedIn" loading="lazy">
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ GET IN TOUCH ════════════════════════════════════════════════════════ -->
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
            <form class="getintouch__form" data-fls-form="dev" method="POST" action="/sendmail">
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

<!-- ═══ MORE RELATED CASES ══════════════════════════════════════════════════ -->
<?php if (!empty($related_cases)): ?>
<section class="cases">
    <div class="cases__container">
        <div class="cases__head">
            <h2 class="cases__title title title--h1">More related cases</h2>
            <a href="/cases/" class="cases__link">View All Projects</a>
            <div class="cases__navigation">
                <button type="button" class="swiper-button-prev"><?= $nav_prev ?></button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next"><?= $nav_next ?></button>
            </div>
        </div>
        <div data-fls-slider="" class="cases__slider swiper">
            <div class="cases__wrapper swiper-wrapper">
                <?php $case_imgs = ['/assets/img/projects/image-1.webp','/assets/img/projects/image-2.webp','/assets/img/projects/image-3.webp','/assets/img/projects/image-4.webp']; ?>
                <?php foreach ($related_cases as $ci => $rc): ?>
                <article class="catalog-card swiper-slide" data-fls-watcher="" data-fls-watcher-threshold="0.6">
                    <a href="/cases/<?= e($rc['slug']) ?>/" class="catalog-card__img">
                        <img alt="<?= e($rc['title'] ?? '') ?>" loading="lazy" src="<?= !empty($rc['image_path']) ? e(media_url($rc['image_path'])) : $case_imgs[$ci % 4] ?>">
                    </a>
                    <?php if (!empty($rc['company_name'])): ?><div class="catalog-card__category"><?= e($rc['company_name']) ?></div><?php endif; ?>
                    <a href="/cases/<?= e($rc['slug']) ?>/" class="catalog-card__title"><?= e($rc['title'] ?? '') ?></a>
                    <div class="catalog-card__bottom">
                        <div class="catalog-card__bottom-inner">
                            <div class="catalog-card__tags">
                                <?php foreach (array_slice($rc['terms'] ?? [], 0, 3) as $t): ?>
                                <div class="catalog-card__tag"><?= e($t['name']) ?></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="catalog-card__arrow">
                                <svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.707031 13L12.707 1M12.707 12.8868V1L0.707031 1" stroke="currentColor" stroke-width="2"/></svg>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
