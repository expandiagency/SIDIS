<?php
$_terms_title       = get_setting('terms_title', $lang_id) ?: 'Terms & Conditions';
$_terms_text        = get_setting('terms_hero_text', $lang_id) ?: 'Please read these terms carefully before using our website or services.';
$_terms_content     = get_setting('terms_content', $lang_id) ?: '<p>Content coming soon.</p>';
$meta_title         = get_setting('terms_meta_title', $lang_id) ?: $_terms_title;
$meta_description   = get_setting('terms_meta_description', $lang_id) ?: '';
require __DIR__ . '/layout.php';
?>
<main class="page">

<section class="promo">
    <div class="promo__container">
        <div class="promo__body">
            <div class="promo__content">
                <h1 class="promo__title"><?= e($_terms_title) ?></h1>
                <div class="promo__text"><?= e($_terms_text) ?></div>
            </div>
            <div class="promo__bg"></div>
        </div>
    </div>
</section>

<section class="article-block">
    <div class="article-block__container">
        <div class="article-block__inner">
            <div class="article-block__content">
                <?= $_terms_content ?>
            </div>
        </div>
    </div>
</section>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
