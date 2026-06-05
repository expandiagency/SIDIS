<?php
$_blog_title  = get_setting('blog_hero_title', $lang_id) ?: 'Trends Blog';
$_blog_text   = get_setting('blog_hero_text', $lang_id) ?: 'Explore how Sidis Group is shaping the future with innovative automation solutions.';
$_blog_img    = media_url(get_setting('blog_hero_image_url') ?: '') ?: null;
$_blog_video  = get_setting('blog_hero_video_path') ?: null;
$_blog_poster = media_url(get_setting('blog_hero_video_poster_url') ?: '') ?: '/assets/img/poster.webp';
$meta_title       = get_setting('blog_meta_title', $lang_id) ?: $_blog_title;
$meta_description = get_setting('blog_meta_description', $lang_id) ?: $_blog_text;
$og_image         = get_setting('blog_og_image_url') ?: '';
require __DIR__ . '/layout.php'; ?>

<main class="page">

<section class="promo">
    <div class="promo__container">
        <div class="promo__body">
            <div class="promo__content">
                <h1 class="promo__title"><?= e($_blog_title) ?></h1>
                <div class="promo__text"><?= e($_blog_text) ?></div>
            </div>
            <div class="promo__bg">
                <?php if ($_blog_video): ?>
                <video autoplay muted loop playsinline poster="<?= e($_blog_poster) ?>">
                    <source src="<?= e($_blog_video) ?>" type="video/mp4">
                </video>
                <?php elseif ($_blog_img): ?>
                <picture>
                    <img alt="promo-img" loading="lazy" src="<?= e($_blog_img) ?>">
                </picture>
                <?php else: ?>
                <picture>
                    <source srcset="./assets/img/promo/image-1-mob.webp" media="(max-width: 650px)">
                    <img alt="promo-img" loading="lazy" src="./assets/img/promo/image-1.webp">
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
                                        onclick="window.location='/blog/'">All</button>
                                    <?php foreach ($f['items'] as $term): ?>
                                    <button data-sel-block-btn="" type="button"
                                        class="sel-block__option<?= $active == $term['id'] ? ' is-active' : '' ?>"
                                        onclick="window.location='/blog/?<?= e($f['key']) ?>=<?= e($term['id']) ?>'">
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
            <?php if (!empty($posts)): ?>
            <?php $blog_fallbacks=['/assets/img/blog/image-4.webp','/assets/img/blog/image-3.webp','/assets/img/blog/image-2.webp','/assets/img/blog/image-1.jpg']; ?>
            <?php foreach ($posts as $pi => $post): ?>
            <article class="catalog-card" data-fls-watcher="" data-fls-watcher-threshold="0.6">
                <a href="/blog/<?= e($post['slug']) ?>/" class="catalog-card__link-wrap">
                    <div class="catalog-card__img">
                        <img alt="<?= e($post['title'] ?? '') ?>" loading="lazy" src="<?= !empty($post['image_path']) ? e(media_url($post['image_path'])) : $blog_fallbacks[$pi % 4] ?>">
                    </div>
                    <div class="catalog-card__info">
                        <div class="catalog-card__time"><?= (int)$post['read_time'] ?> min to read</div>
                        <?php if (!empty($post['published_at'])): ?>
                        <div class="catalog-card__date"><?= date('d/m/y', strtotime($post['published_at'])) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="catalog-card__title"><?= e($post['title'] ?? '') ?></div>
                    <div class="catalog-card__bottom">
                        <div class="catalog-card__bottom-inner">
                            <?php if (!empty($post['author_name'])): ?>
                            <div class="catalog-card__author">
                                <?php if (!empty($post['author_image_path'])): ?>
                                <div class="catalog-card__author-img">
                                    <img alt="<?= e($post['author_name']) ?>" loading="lazy" src="<?= e(media_url($post['author_image_path'])) ?>">
                                </div>
                                <?php endif; ?>
                                <div class="catalog-card__author-body">
                                    <div class="catalog-card__author-name"><?= e($post['author_name']) ?></div>
                                    <?php if (!empty($post['author_title'])): ?>
                                    <div class="catalog-card__author-work"><?= e($post['author_title']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="catalog-card__arrow">
                                <svg width="14" height="14" viewbox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.707031 13L12.707 1M12.707 12.8868V1L0.707031 1" stroke="currentColor" stroke-width="2"></path></svg>
                            </div>
                        </div>
                    </div>
                </a>
            </article>
            <?php endforeach; ?>
            <?php else: ?>
            <p style="color:#999;padding:40px 0">No posts found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
