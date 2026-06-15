<?php
$_cases_title  = get_setting('cases_hero_title', $lang_id) ?: 'Case Studies';
$_cases_text   = get_setting('cases_hero_text', $lang_id) ?: 'See how Sidis Group has helped its clients achieve their vision of digital innovation.';
$_cases_img    = media_url(get_setting('cases_hero_image_url') ?: '') ?: null;
$_cases_video  = ltrim(get_setting('cases_hero_video_path') ?: '', '.') ?: null;
$_cases_poster = media_url(get_setting('cases_hero_video_poster_url') ?: '') ?: '/assets/img/poster.webp';
$meta_title       = get_setting('cases_meta_title', $lang_id) ?: $_cases_title;
$meta_description = get_setting('cases_meta_description', $lang_id) ?: $_cases_text;
$og_image         = get_setting('cases_og_image_url') ?: '';
require __DIR__ . '/layout.php'; ?>

<main class="page">

<section class="promo">
    <div class="promo__container">
        <div class="promo__body">
            <div class="promo__content">
                <h1 class="promo__title"><?= e($_cases_title) ?></h1>
                <div class="promo__text"><?= e($_cases_text) ?></div>
            </div>
            <div class="promo__bg">
                <?php if ($_cases_video): ?>
                <video autoplay muted loop playsinline poster="<?= e($_cases_poster) ?>">
                    <source src="<?= e($_cases_video) ?>" type="video/mp4">
                </video>
                <?php elseif ($_cases_img): ?>
                <picture>
                    <img alt="promo-img" loading="lazy" src="<?= e($_cases_img) ?>">
                </picture>
                <?php else: ?>
                <picture>
                    <source srcset="./assets/img/promo/image-2-mob.webp" media="(max-width: 650px)">
                    <img alt="promo-img" loading="lazy" src="./assets/img/promo/image-2.webp">
                </picture>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="catalog">
    <div class="catalog__container">
        <div class="catalog__head">
            <h2 class="catalog__title title title--h2">Category</h2>
            <div class="catalog__actions">
                <?php
                $arrow_svg = '<svg width="9" height="5" viewbox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.353516 0.353577L4.04492 4.04498L7.73633 0.353577" stroke="currentColor"></path></svg>';
                $filters = [
                    ['label'=>'Solutions',   'key'=>'solution',   'items'=>$terms['solutions']],
                    ['label'=>'Departments', 'key'=>'department', 'items'=>$terms['departments']],
                    ['label'=>'Industries',  'key'=>'industry',   'items'=>$terms['industries']],
                ];
                foreach ($filters as $f):
                    $active = $_GET[$f['key']] ?? '';
                ?>
                <div class="catalog__filter">
                    <div class="catalog__label"><?= e($f['label']) ?></div>
                    <div data-sel-block="" data-sel-block-placeholder="All" class="catalog__sort sel-block">
                        <button type="button" class="sel-block__current" data-sel-block-current="">
                            <span class="sel-block__value" data-sel-block-value=""><span>All</span></span>
                            <span class="sel-block__arrow"><?= $arrow_svg ?></span>
                        </button>
                        <div class="sel-block__dropdown" data-sel-block-dropdown="">
                            <div class="sel-block__scroll">
                                <div class="sel-block__options">
                                    <button data-sel-block-btn="" data-sel-block-all="" type="button"
                                        class="sel-block__option<?= !$active ? ' is-active' : '' ?>"
                                        data-filter-url="/cases/">All</button>
                                    <?php foreach ($f['items'] as $term): ?>
                                    <button data-sel-block-btn="" type="button"
                                        class="sel-block__option<?= $active == $term['id'] ? ' is-active' : '' ?>"
                                        data-filter-url="/cases/?<?= e($f['key']) ?>=<?= e($term['id']) ?>">
                                        <?= e($term['name']) ?>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="catalog__items">
            <?php if (!empty($cases)): ?>
            <?php foreach ($cases as $case):
                $tags = array_column($case['terms'] ?? [], 'name');
            ?>
            <?php $cl = case_link($case); ?>
            <article class="catalog-card" data-fls-watcher="" data-fls-watcher-threshold="0.6">
                <?php if ($cl['href']): ?><a href="<?= e($cl['href']) ?>"<?= $cl['external'] ? ' target="_blank" rel="noopener noreferrer"' : '' ?> class="catalog-card__link-wrap"><?php else: ?><div class="catalog-card__link-wrap"><?php endif; ?>
                    <div class="catalog-card__img">
                        <?php if (!empty($case['image_path'])): ?>
                        <img alt="<?= e($case['title'] ?? '') ?>" loading="lazy" src="<?= e(media_url($case['image_path'])) ?>">
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($case['company_name'])): ?>
                    <div class="catalog-card__category"><?= e($case['company_name']) ?></div>
                    <?php endif; ?>
                    <div class="catalog-card__title"><?= e($case['title'] ?? '') ?></div>
                    <div class="catalog-card__bottom">
                        <div class="catalog-card__bottom-inner">
                            <?php if (!empty($tags)): ?>
                            <div class="catalog-card__tags">
                                <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                                <div class="catalog-card__tag"><?= e($tag) ?></div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <div class="catalog-card__arrow">
                                <svg width="14" height="14" viewbox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.707031 13L12.707 1M12.707 12.8868V1L0.707031 1" stroke="currentColor" stroke-width="2"></path></svg>
                            </div>
                        </div>
                    </div>
                <?php if ($cl['href']): ?></a><?php else: ?></div><?php endif; ?>
            </article>
            <?php endforeach; ?>
            <?php else: ?>
            <p style="color:#999;padding:40px 0">No results found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
