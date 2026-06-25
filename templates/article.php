<?php require __DIR__ . '/layout.php';

$arrow_svg = '<svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.8L12.5664 0.800049M12.5664 0.800049L12.5664 12.8M12.5664 0.800049L0.679613 0.800049" stroke="currentColor" stroke-width="1.6"></path></svg>';
$nav_prev  = '<svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M9.53516 0.523193L0.535156 8.52319L9.53516 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>';
$nav_next  = '<svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>';

$extras       = $post['extras'] ?? [];
$faq_items    = $extras['faq'] ?? [];
$faq_title    = $extras['faq_title'] ?? 'Questions & answers';
$faq_enabled  = $extras['faq_enabled'] ?? true;
$related_posts_data = $related_posts ?? [];
$articles_title = $extras['articles_title'] ?? 'Latest Automation Insights';
?>
<main class="page">

<section class="promo-article">
    <div class="promo-article__container">
        <div class="promo-article__body">
            <div class="promo-article__bg">
                <?php if (!empty($post['image_path'])): ?>
                <img alt="<?= e($post['title']) ?>" loading="lazy" src="<?= e(media_url($post['image_path'])) ?>">
                <?php else: ?>
                <img alt="<?= e($post['title']) ?>" loading="lazy" src="/assets/img/blog/image-4.webp">
                <?php endif; ?>
            </div>
            <div class="promo-article__content">
                <?php if (!empty($post['published_at'])): ?>
                <div data-fls-dynamic=".promo-article__bottom, 768" class="promo-article__date">Published: <?= date('d.m.Y', strtotime($post['published_at'])) ?></div>
                <?php endif; ?>
                <h1 class="promo-article__title"><?= e($post['title'] ?? '') ?></h1>
                <?php if (!empty($post['excerpt'])): ?>
                <div class="promo-article__text"><?= e($post['excerpt']) ?></div>
                <?php endif; ?>
                <?php if (!empty($post['terms'])): ?>
                <div class="promo-article__tags">
                    <?php foreach ($post['terms'] as $term): ?>
                    <div class="promo-article__tag"><?= e($term['name'] ?? '') ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="promo-article__bottom">
                <?php if (!empty($post['author_name'])): ?>
                <div data-fls-dynamic=".promo-article__container, 768" class="promo-article__author">
                    <?php if (!empty($post['author_image_path'])): ?>
                    <div class="promo-article__author-img">
                        <img alt="<?= e($post['author_name']) ?>" loading="lazy" src="<?= e(media_url($post['author_image_path'])) ?>"<?= ($_srcset = media_srcset($post['author_image_path'])) ? ' srcset="' . e($_srcset) . '" sizes="120px"' : '' ?>>
                    </div>
                    <?php endif; ?>
                    <div class="promo-article__author-body">
                        <div class="promo-article__author-head">
                            <div class="promo-article__author-name"><?= e($post['author_name']) ?></div>
                            <?php if (!empty($post['author_title'])): ?>
                            <div class="promo-article__author-work"><?= e($post['author_title']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($post['author_linkedin'])): ?>
                        <div class="promo-article__author-socials">
                            <a href="<?= e($post['author_linkedin']) ?>" target="_blank" rel="noopener" class="promo-article__author-social">
                                <img src="/assets/img/icons/linkedin.svg" width="38" height="38" alt="LinkedIn" loading="lazy">
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="article-block">
    <div class="article-block__container">
        <div class="article-block__inner">
            <aside class="article-block__aside aside-nav" data-article-menu="" data-fls-header-scroll="" data-fls-header-scroll-show="">
                <div class="aside-nav__body">
                    <?php if (!empty($post['toc'])): ?>
                    <div class="aside-nav__title">Article menu</div>
                    <ul class="aside-nav__list">
                        <?php foreach ($post['toc'] as $toc): ?>
                        <li class="aside-nav__item">
                            <a href="#<?= e($toc['anchor']) ?>" class="aside-nav__link" data-fls-scrollto-header="" data-fls-scrollto="#<?= e($toc['anchor']) ?>"><?= e($toc['title']) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <?php
                    $_aside_img      = media_url(get_setting('aside_demo_image_url') ?: '') ?: '/assets/img/projects/image-5.webp';
                    $_aside_demo_txt = get_setting('aside_demo_btn_text', $lang_id) ?: 'Demo';
                    $_aside_demo_url = get_setting('aside_demo_btn_url') ?: '#getintouch';
                    $_aside_cta_txt  = get_setting('aside_contact_btn_text', $lang_id) ?: 'Contact us';
                    $_aside_cta_url  = get_setting('aside_contact_btn_url') ?: '#getintouch';
                    ?>
                    <div class="aside-nav__demo">
                        <img alt="Image" loading="lazy" src="<?= e($_aside_img) ?>">
                        <a href="<?= e($_aside_demo_url) ?>" class="aside-nav__demo-btn button button--icon button--border button--border-white">
                            <span class="button__text"><?= e($_aside_demo_txt) ?></span>
                            <span class="button__icon"><?= $arrow_svg ?></span>
                        </a>
                    </div>
                </div>
                <div class="aside-nav__bottom">
                    <button type="button" class="aside-nav__burger" data-article-menu-btn=""><span></span><span></span><span></span><span></span></button>
                    <a href="<?= e($_aside_cta_url) ?>" class="aside-nav__btn button button--third button--dark-hover"><?= e($_aside_cta_txt) ?></a>
                </div>
            </aside>
            <div class="article-block__content">
                <div class="article-block__top">
                    <div class="article-block__label">Summarize:</div>
                    <div class="article-block__links">
                        <?php $article_url = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'sidis.expandi.agency') . '/blog/' . e($post['slug']) . '/'; ?>
                        <a href="https://chat.openai.com/?q=Summarize+<?= urlencode($article_url) ?>" target="_blank" class="article-block__link">
                            <span>ChatGPT</span>
                            <svg width="19" height="19" viewbox="0 0 19 19" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.1246 7.50412C17.333 6.88723 17.4053 6.23252 17.3366 5.58502C17.2679 4.93751 17.0598 4.31256 16.7266 3.75312C16.2257 2.90069 15.469 2.22771 14.5639 1.82974C13.6588 1.43177 12.6514 1.32903 11.6846 1.53612C11.2439 1.04824 10.7048 0.659146 10.103 0.394416C9.50115 0.129686 8.85009 -0.00468784 8.19261 0.000124853C6.15861 -0.00287515 4.35261 1.28912 3.72661 3.19912C3.08341 3.32807 2.47495 3.59223 1.94155 3.97409C1.40815 4.35595 0.961984 4.84681 0.632614 5.41412C0.134718 6.25917 -0.0780939 7.24199 0.0256328 8.21731C0.12936 9.19263 0.544121 10.1087 1.20861 10.8301C0.999943 11.4471 0.927378 12.1019 0.995911 12.7496C1.06444 13.3973 1.27245 14.0225 1.60561 14.5821C2.61961 16.3241 4.65761 17.2201 6.64761 16.7991C7.08835 17.2867 7.62724 17.6755 8.2289 17.9401C8.83056 18.2046 9.48138 18.3389 10.1386 18.3341C12.1746 18.3391 13.9806 17.0451 14.6066 15.1341C15.2503 15.0052 15.8593 14.7409 16.3931 14.3586C16.9269 13.9764 17.3732 13.485 17.7026 12.9171C18.1998 12.0721 18.412 11.0896 18.3079 10.1147C18.2038 9.13975 17.789 8.22517 17.1246 7.50412ZM10.1406 17.1341C9.32784 17.136 8.53977 16.8549 7.91161 16.3391L8.02161 16.2781L11.7216 14.1691C11.814 14.1177 11.8908 14.0424 11.9441 13.9511C11.9975 13.8598 12.0252 13.7558 12.0246 13.6501V8.50513L13.5886 9.39512L13.6186 9.43713V13.7001C13.6166 15.5951 12.0606 17.1311 10.1406 17.1351M2.66061 13.9841C2.25213 13.2905 2.10462 12.4738 2.24461 11.6811L2.35461 11.7461L6.05461 13.8551C6.14711 13.9083 6.25193 13.9363 6.35861 13.9363C6.4653 13.9363 6.57012 13.9083 6.66261 13.8551L11.1786 11.2811V13.0631L11.1566 13.1101L7.41661 15.2401C5.75061 16.1871 3.62361 15.6241 2.66061 13.9821V13.9841ZM1.68561 6.01412C2.09526 5.31579 2.73681 4.78323 3.49861 4.50912L3.49661 4.63512V8.85112C3.49581 8.95702 3.52351 9.06117 3.57683 9.15266C3.63014 9.24416 3.70709 9.31962 3.79961 9.37113L8.31661 11.9441L6.75361 12.8351L6.70061 12.8401L2.96061 10.7071C2.56647 10.4851 2.22036 10.1869 1.94241 9.83001C1.66445 9.47308 1.46019 9.06448 1.34147 8.62795C1.22275 8.19141 1.19195 7.73564 1.25086 7.2871C1.30977 6.83857 1.45721 6.4062 1.68461 6.01512L1.68561 6.01412ZM14.5336 8.96412L10.0166 6.39012L11.5796 5.50012L11.6326 5.49512L15.3726 7.62612C15.7671 7.84805 16.1136 8.14625 16.3918 8.50335C16.67 8.86045 16.8743 9.26932 16.993 9.70616C17.1116 10.143 17.1422 10.5991 17.0829 11.0478C17.0236 11.4966 16.8757 11.9291 16.6476 12.3201C16.2377 13.0181 15.5968 13.5508 14.8356 13.8261V9.48413C14.8365 9.37833 14.809 9.27425 14.7558 9.18276C14.7027 9.09128 14.6259 9.01576 14.5336 8.96412ZM16.0886 6.65312L15.9786 6.58812L12.2786 4.48012C12.1862 4.42714 12.0816 4.39926 11.9751 4.39926C11.8686 4.39926 11.764 4.42714 11.6716 4.48012L7.15461 7.05312V5.27112L7.17761 5.22412L10.9166 3.09412C12.5836 2.14712 14.7126 2.71112 15.6726 4.35512C16.0786 5.05013 16.2266 5.86312 16.0886 6.65312ZM6.30461 9.82712L4.74061 8.93713L4.71061 8.89412V4.63312C4.71161 2.73512 6.27161 1.19812 8.19461 1.20012C9.00861 1.20012 9.79461 1.48112 10.4206 1.99512L10.3116 2.05512L6.61161 4.16412C6.51906 4.21542 6.442 4.29067 6.38851 4.38198C6.33503 4.47329 6.30709 4.5773 6.30761 4.68312L6.30461 9.82712ZM7.15461 8.02012L9.16661 6.87512L11.1786 8.02112V10.3131L9.16661 11.4591L7.15461 10.3131V8.02012Z" fill="currentColor"/></svg>
                        </a>
                        <a href="https://www.perplexity.ai/search/new?q=Summarize+<?= urlencode($article_url) ?>" target="_blank" class="article-block__link">
                            <span>Perplexity</span>
                            <svg width="18" height="20" viewbox="0 0 18 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.238 0V6.06H17.5V14.683H15.054V20L9.19 14.838V19.959H8.28V14.833L2.41 20V14.613H0V5.99H2.403V0L8.281 5.412V0.158H9.189V5.567L15.238 0ZM9.19 7.537V13.636L14.145 17.997V12.033L9.19 7.537ZM8.274 7.47L3.32 11.968V17.998L8.275 13.636L8.274 7.47ZM15.054 13.787H16.591V6.957H9.967L15.054 11.574V13.787ZM7.569 6.887H0.91V13.716H2.41V11.569L7.57 6.886M3.312 2.063V5.988H7.575L3.312 2.063ZM14.328 2.063L10.066 5.988H14.328V2.063Z" fill="currentColor"/></svg>
                        </a>
                    </div>
                </div>
                <?= $post['content'] ?? '' ?>
            </div>
        </div>
    </div>
</section>

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
            <form class="getintouch__form" data-fls-form="ajax" method="POST" action="/sendmail/">
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

<?php if (!empty($faq_items) && $faq_enabled): ?>
<?php $faq_half = (int)ceil(count($faq_items) / 2); ?>
<section class="faq">
    <div class="faq__container">
        <h2 class="faq__title title title--h1"><?= e($faq_title) ?></h2>
        <div data-fls-spollers="" data-fls-spollers-one="" class="faq__items spollers">
            <div class="spollers__column">
                <?php foreach (array_slice($faq_items, 0, $faq_half) as $item): ?>
                <details class="spollers__item">
                    <summary class="spollers__title"><?= e($item['q'] ?? '') ?></summary>
                    <div class="spollers__body"><?= nl2br(e($item['a'] ?? '')) ?></div>
                </details>
                <?php endforeach; ?>
            </div>
            <div class="spollers__column">
                <?php foreach (array_slice($faq_items, $faq_half) as $item): ?>
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

<?php if (!empty($related_posts_data)): ?>
<section class="articles">
    <div class="articles__container">
        <div class="articles__head">
            <h2 class="articles__title title title--h1"><?= nl2br(e($articles_title)) ?></h2>
            <div class="articles__navigation">
                <button type="button" class="swiper-button-prev"><?= $nav_prev ?></button>
                <div class="swiper-pagination"></div>
                <button type="button" class="swiper-button-next"><?= $nav_next ?></button>
            </div>
        </div>
        <div data-fls-slider="" class="articles__slider swiper">
            <div class="articles__wrapper swiper-wrapper">
                <?php $blog_fallbacks = ['/assets/img/blog/image-4.webp','/assets/img/blog/image-3.webp','/assets/img/blog/image-2.webp','/assets/img/blog/image-1.jpg']; ?>
                <?php foreach (array_values($related_posts_data) as $pi => $rp): ?>
                <article class="article-card swiper-slide">
                    <div class="article-card__img">
                        <img alt="<?= e($rp['title'] ?? '') ?>" loading="lazy" src="<?= !empty($rp['image_path']) ? e(media_url($rp['image_path'])) : $blog_fallbacks[$pi % 4] ?>">
                        <a href="/blog/<?= e($rp['slug'] ?? '') ?>/" class="article-card__arrow button button--icon">
                            <span class="button__text">Open article</span>
                            <span class="button__icon"><svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.707031 13L12.707 1M12.707 1L0.707031 1M12.707 1V12.8868" stroke="currentColor" stroke-width="2"/></svg></span>
                        </a>
                    </div>
                    <a href="/blog/<?= e($rp['slug'] ?? '') ?>/" class="article-card__title"><?= e($rp['title'] ?? '') ?></a>
                    <?php if (!empty($rp['published_at'])): ?>
                    <div class="article-card__badges">
                        <div class="article-card__badge"><?= date('j, F, Y', strtotime($rp['published_at'])) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($rp['author_name'])): ?>
                    <div class="article-card__author">
                        <?php if (!empty($rp['author_image_path'])): ?>
                        <div class="article-card__author-img">
                            <img alt="<?= e($rp['author_name']) ?>" loading="lazy" src="<?= e(media_url($rp['author_image_path'])) ?>"<?= ($_srcset = media_srcset($rp['author_image_path'])) ? ' srcset="' . e($_srcset) . '" sizes="102px"' : '' ?>>
                        </div>
                        <?php endif; ?>
                        <div class="article-card__author-body">
                            <div class="article-card__author-name"><?= e($rp['author_name']) ?></div>
                            <?php if (!empty($rp['author_title'])): ?>
                            <div class="article-card__author-position"><?= e($rp['author_title']) ?></div>
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

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
