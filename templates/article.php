<?php require __DIR__ . '/layout.php';

$arrow_svg = '<svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.8L12.5664 0.800049M12.5664 0.800049L12.5664 12.8M12.5664 0.800049L0.679613 0.800049" stroke="currentColor" stroke-width="1.6"></path></svg>';
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
                            <svg width="19" height="19" viewbox="0 0 19 19" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.1246 7.50412C17.333 6.88723 17.4053 6.23252 17.3366 5.58502C17.2679 4.93751 17.0598 4.31256 16.7266 3.75312C16.2257 2.90069 15.469 2.22771 14.5639 1.82974C13.6588 1.43177 12.6514 1.32903 11.6846 1.53612C11.2439 1.04824 10.7048 0.659146 10.103 0.394416C9.50115 0.129686 8.85009 -0.00468784 8.19261 0.000124853C6.15861 -0.00287515 4.35261 1.28912 3.72661 3.19912C3.08341 3.32807 2.47495 3.59223 1.94155 3.97409C1.40815 4.35595 0.961984 4.84681 0.632614 5.41412C0.134718 6.25917 -0.0780939 7.24199 0.0256328 8.21731C0.12936 9.19263 0.544121 10.1087 1.20861 10.8301C0.999943 11.4471 0.927378 12.1019 0.995911 12.7496C1.06444 13.3973 1.27245 14.0225 1.60561 14.5821C2.61961 16.3241 4.65761 17.2201 6.64761 16.7991C7.08835 17.2867 7.62724 17.6755 8.2289 17.9401C8.83056 18.2046 9.48138 18.3389 10.1386 18.3341C12.1746 18.3391 13.9806 17.0451 14.6066 15.1341C15.2503 15.0052 15.8593 14.7409 16.3931 14.3586C16.9269 13.9764 17.3732 13.485 17.7026 12.9171C18.1998 12.0721 18.412 11.0896 18.3079 10.1147C18.2038 9.13975 17.789 8.22517 17.1246 7.50412Z" fill="currentColor"/></svg>
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

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
