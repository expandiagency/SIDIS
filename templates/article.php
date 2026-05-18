<?php require __DIR__ . '/layout.php';

$arrow_svg = '<svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.8L12.5664 0.800049M12.5664 0.800049L12.5664 12.8M12.5664 0.800049L0.679613 0.800049" stroke="currentColor" stroke-width="1.6"></path></svg>';
$nav_prev  = '<svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M9.53516 0.523193L0.535156 8.52319L9.53516 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>';
$nav_next  = '<svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>';

$extras       = $post['extras'] ?? [];
$faq_items    = $extras['faq'] ?? [];
$faq_title    = $extras['faq_title'] ?? 'Questions & answers';
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
                <?php if (!empty($post['tags'])): ?>
                <div class="promo-article__tags">
                    <?php foreach ($post['tags'] as $tag): ?>
                    <div class="promo-article__tag"><?= e(is_array($tag) ? ($tag['tag_text'] ?? '') : $tag) ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="promo-article__bottom">
                <?php if (!empty($post['author_name'])): ?>
                <div data-fls-dynamic=".promo-article__container, 768" class="promo-article__author">
                    <?php if (!empty($post['author_image_path'])): ?>
                    <div class="promo-article__author-img">
                        <img alt="<?= e($post['author_name']) ?>" loading="lazy" src="<?= e(media_url($post['author_image_path'])) ?>">
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
                                <img src="/assets/img/icons/linkedin.svg" alt="LinkedIn" loading="lazy">
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
            <div class="article-block__content">
                <div class="article-block__top">
                    <div class="article-block__label">Summarize:</div>
                    <div class="article-block__links">
                        <?php $article_url = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'sidis.expandi.agency') . '/blog/' . e($post['slug']) . '/'; ?>
                        <a href="https://chat.openai.com/?q=Summarize+<?= urlencode($article_url) ?>" target="_blank" class="article-block__link">
                            <span>ChatGPT</span>
                            <svg width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="20" height="20" rx="4" fill="currentColor"/><path d="M10.5 5.5C10.5 5.5 7.5 5.5 7.5 8.5C7.5 10 8.5 11 10 11.5V13.5H11.5V11.5C13 11 14 10 14 8.5C14 5.5 10.5 5.5 10.5 5.5Z" fill="white"/><circle cx="10" cy="15" r="1" fill="white"/></svg>
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

<?php if (!empty($faq_items)): ?>
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
                            <img alt="<?= e($rp['author_name']) ?>" loading="lazy" src="<?= e(media_url($rp['author_image_path'])) ?>">
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
