<?php
// $lang, $languages, $nav_header, $nav_footer, $page_class must be set by caller
$meta_title = $meta_title ?? get_setting('site_name', $lang_id) ?: 'Sidis';
$meta_description = $meta_description ?? get_setting('site_description', $lang_id) ?: '';
$lang_code = $lang['code'] ?? 'en';
$is_default = (bool)($lang['is_default'] ?? true);

function active_lang_url(string $path, array $lang, array $default_lang): string {
    $path = '/' . ltrim($path, '/');
    if ((int)($lang['is_default'] ?? 0)) return $path;
    return '/' . $lang['code'] . $path;
}
?>
<!doctype html>
<html lang="<?= e($lang_code) ?>" class="<?= e($page_class ?? '') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preload" href="/assets/fonts/Inter-SemiBold.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="/assets/fonts/Inter-Regular.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="/assets/fonts/Inter-Medium.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="/assets/fonts/Inter-Bold.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="/assets/fonts/BasementGrotesqueExpanded-Black.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="/assets/fonts/BasementGrotesque-Medium.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="/assets/fonts/BasementGrotesque-Bold.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="/assets/fonts/BasementGrotesque-Black.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    <title><?= e($meta_title) ?></title>
    <?php if ($meta_description): ?><meta name="description" content="<?= e($meta_description) ?>"><?php endif; ?>
    <script type="module" crossorigin="" src="/js/app.min.js"></script>
    <link rel="stylesheet" crossorigin="" href="/css/app.min.css">
    <!-- Language alternates -->
    <?php foreach ($languages as $l): ?>
    <link rel="alternate" hreflang="<?= e($l['code']) ?>" href="<?= e(SITE_URL . ((int)$l['is_default'] ? '/' : '/' . $l['code'] . '/')) ?>">
    <?php endforeach; ?>
</head>
<body>
<div class="wrapper" data-top-position="">

<!-- ═══════════════════════════════ HEADER ═════════════════════════════ -->
<header class="header" data-fls-lp="" data-fls-header-scroll="200" data-fls-header-scroll-show="">
    <div class="header__container">
        <div class="header__body">
            <a href="<?= $is_default ? '/' : '/' . e($lang_code) . '/' ?>" class="header__logo">
                <svg width="115" height="29" viewbox="0 0 115 29" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M53.6506 13.8293C53.6506 14.7154 53.3599 15.4805 52.7785 16.1294C52.1971 16.7759 51.3877 17.2736 50.3458 17.6155C49.3062 17.9597 48.1131 18.1318 46.7665 18.1318C46.3897 18.1318 45.9292 18.1132 45.3827 18.076C44.8361 18.0388 44.1105 17.9342 43.2058 17.7644C42.3011 17.5946 41.3592 17.3411 40.3801 17.0085V13.5339C41.2987 13.9758 42.2011 14.3456 43.0872 14.6386C43.9733 14.9316 44.9245 15.0782 45.9385 15.0782C46.8735 15.0782 47.4782 14.9572 47.7549 14.7154C48.0317 14.4735 48.1689 14.2479 48.1689 14.0362C48.1689 13.6548 47.934 13.3339 47.4619 13.0711C46.9898 12.8083 46.3037 12.5385 45.4036 12.2594C44.4082 11.9315 43.5337 11.5756 42.7825 11.1919C42.0313 10.8081 41.4173 10.3244 40.9406 9.74529C40.4638 9.16618 40.2266 8.48242 40.2266 7.69167C40.2266 6.90093 40.4661 6.2381 40.9475 5.62178C41.429 5.00546 42.1616 4.51241 43.15 4.14495C44.1384 3.77748 45.3432 3.59375 46.7688 3.59375C47.7828 3.59375 48.7201 3.6705 49.5806 3.824C50.4411 3.97749 51.1505 4.1496 51.711 4.34031C52.2715 4.53102 52.6552 4.67986 52.8645 4.78917V8.1103C52.1226 7.694 51.2947 7.32421 50.3807 6.99628C49.4667 6.66835 48.4899 6.50555 47.4503 6.50555C46.7688 6.50555 46.2758 6.60789 45.9688 6.81255C45.6618 7.01721 45.5106 7.26839 45.5106 7.56841C45.5106 7.8475 45.6641 8.08937 45.9688 8.29403C46.2734 8.4987 46.82 8.74755 47.6061 9.04059C49.0038 9.55225 50.1225 10.0104 50.9621 10.4197C51.8017 10.8291 52.4575 11.3012 52.9343 11.8361C53.4111 12.371 53.6483 13.0362 53.6483 13.8339L53.6506 13.8293Z" fill="currentColor"/><path d="M63.5419 17.9472H58.0625V3.78125H63.5419V17.9472Z" fill="currentColor"/><path d="M87.4849 11.1212C87.4849 12.5725 87.1453 13.7377 86.4686 14.6168C85.7918 15.4959 84.994 16.2076 84.0754 16.7518C82.773 17.5495 80.9519 17.9472 78.6099 17.9472H68.8047V3.78125H76.354C77.4889 3.78125 78.4611 3.82776 79.2704 3.91847C80.0775 4.00917 80.8054 4.13941 81.452 4.30686C82.0985 4.47432 82.6497 4.66037 83.1032 4.86969C83.5567 5.07668 83.9637 5.29995 84.3219 5.5395C84.9429 5.93487 85.4871 6.40234 85.9546 6.93958C86.422 7.47682 86.7941 8.10244 87.0686 8.80946C87.343 9.51648 87.4826 10.2886 87.4826 11.1189L87.4849 11.1212ZM81.9148 10.9375C81.9148 9.78626 81.6334 8.88854 81.0706 8.24431C80.5077 7.60009 79.8286 7.16983 79.0379 6.95121C78.2471 6.73259 77.4401 6.62328 76.6191 6.62328H74.2864V15.0145H76.6191C78.0518 15.0145 79.2937 14.7494 80.3426 14.2168C81.3915 13.6842 81.9148 12.5911 81.9148 10.9352V10.9375Z" fill="currentColor"/><path d="M97.0419 17.9472H91.5625V3.78125H97.0419V17.9472Z" fill="currentColor"/><path d="M115.002 13.8293C115.002 14.7154 114.711 15.4805 114.13 16.1294C113.549 16.7759 112.739 17.2736 111.697 17.6155C110.658 17.9597 109.465 18.1318 108.118 18.1318C107.741 18.1318 107.281 18.1132 106.734 18.076C106.188 18.0388 105.462 17.9342 104.557 17.7644C103.653 17.5946 102.711 17.3411 101.732 17.0085V13.5339C102.65 13.9758 103.553 14.3456 104.439 14.6386C105.325 14.9316 106.276 15.0782 107.29 15.0782C108.225 15.0782 108.83 14.9572 109.106 14.7154C109.383 14.4735 109.52 14.2479 109.52 14.0362C109.52 13.6548 109.286 13.3339 108.813 13.0711C108.341 12.8083 107.655 12.5385 106.755 12.2594C105.76 11.9315 104.885 11.5756 104.134 11.1919C103.383 10.8081 102.769 10.3244 102.292 9.74529C101.815 9.16618 101.578 8.48242 101.578 7.69167C101.578 6.90093 101.818 6.2381 102.299 5.62178C102.781 5.00546 103.513 4.51241 104.502 4.14495C105.49 3.77748 106.695 3.59375 108.12 3.59375C109.134 3.59375 110.072 3.6705 110.932 3.824C111.793 3.97749 112.502 4.1496 113.063 4.34031C113.623 4.53102 114.007 4.67986 114.216 4.78917V8.1103C113.474 7.694 112.646 7.32421 111.732 6.99628C110.818 6.66835 109.841 6.50555 108.802 6.50555C108.12 6.50555 107.627 6.60789 107.32 6.81255C107.013 7.01721 106.862 7.26839 106.862 7.56841C106.862 7.8475 107.016 8.08937 107.32 8.29403C107.625 8.4987 108.172 8.74755 108.958 9.04059C110.355 9.55225 111.474 10.0104 112.314 10.4197C113.153 10.8291 113.809 11.3012 114.286 11.8361C114.763 12.371 115 13.0362 115 13.8339L115.002 13.8293Z" fill="currentColor"/><path d="M85.0943 25.8681C84.9082 26.1192 84.6478 26.3286 84.3129 26.4914C83.9779 26.6565 83.543 26.7402 83.0081 26.7402C82.5709 26.7402 82.1802 26.6821 81.836 26.5681C81.4918 26.4541 81.201 26.289 80.9615 26.0727C80.7219 25.8564 80.5382 25.5936 80.4149 25.2843C80.2894 24.975 80.2266 24.6238 80.2266 24.2284V24.0959C80.2266 23.6819 80.2894 23.3167 80.4149 23.0005C80.5405 22.6842 80.7219 22.419 80.9615 22.2004C81.201 21.9841 81.4941 21.8213 81.836 21.712C82.1802 21.6027 82.5709 21.5469 83.0081 21.5469C83.7477 21.5469 84.3477 21.698 84.8106 22.0004C85.2734 22.3027 85.5571 22.7586 85.6594 23.3679H84.9199C84.8734 23.1865 84.7943 23.0191 84.6873 22.8702C84.578 22.719 84.4408 22.5911 84.2756 22.4865C84.1105 22.3818 83.9221 22.3004 83.7105 22.2423C83.4988 22.1841 83.264 22.1539 83.0081 22.1539C82.6686 22.1539 82.3685 22.2027 82.1127 22.3004C81.8546 22.3981 81.6383 22.5353 81.4662 22.7121C81.2941 22.8888 81.1661 23.1005 81.0778 23.3447C80.9917 23.5889 80.9499 23.8586 80.9499 24.1563C80.9499 24.454 80.9917 24.7029 81.0778 24.9448C81.1638 25.1866 81.2917 25.3936 81.4662 25.568C81.6406 25.7425 81.8546 25.8797 82.1127 25.9797C82.3709 26.0797 82.6686 26.1285 83.0081 26.1285C83.3477 26.1285 83.6175 26.0913 83.8593 26.0169C84.1012 25.9425 84.3059 25.8402 84.478 25.7099C84.6478 25.5797 84.7826 25.4285 84.8827 25.2611C84.9827 25.0913 85.0478 24.9122 85.0803 24.7192H82.436V24.0912H85.7781V26.5751H85.0943V25.8634V25.8681Z" fill="currentColor"/><path d="M88.7619 24.801V26.5871H88.0781V21.7031H91.3365C91.5923 21.7031 91.8249 21.738 92.0342 21.8031C92.2435 21.8706 92.4226 21.9706 92.5668 22.1031C92.7133 22.2357 92.8272 22.4032 92.9086 22.6032C92.99 22.8032 93.0296 23.0358 93.0296 23.3009C93.0156 23.6916 92.9226 24.0009 92.7482 24.2289C92.5737 24.4568 92.3319 24.6173 92.0272 24.7103L93.1087 26.5802H92.3063L91.2946 24.801H88.7619ZM88.7619 24.187H91.1062C91.5109 24.187 91.8109 24.1079 92.0086 23.9498C92.2063 23.7916 92.304 23.5614 92.304 23.259C92.304 22.9567 92.2109 22.7288 92.0249 22.5636C91.8388 22.3985 91.5574 22.3148 91.1806 22.3148H88.7596V24.1847L88.7619 24.187Z" fill="currentColor"/><path d="M95.0703 24.0959C95.0843 23.654 95.1633 23.2726 95.3029 22.9516C95.4448 22.6307 95.6401 22.3679 95.889 22.1585C96.1378 21.9516 96.4332 21.7981 96.7751 21.698C97.1169 21.598 97.4984 21.5469 97.917 21.5469C98.3356 21.5469 98.717 21.6004 99.0589 21.7027C99.4008 21.8074 99.6962 21.9632 99.945 22.1748C100.194 22.3865 100.389 22.6493 100.531 22.9679C100.673 23.2865 100.75 23.661 100.764 24.0935V24.2191C100.75 24.6517 100.671 25.0285 100.531 25.3448C100.389 25.6634 100.194 25.9262 99.945 26.1309C99.6962 26.3355 99.4008 26.489 99.0589 26.5867C98.717 26.6867 98.3356 26.7379 97.917 26.7379C97.4984 26.7379 97.1193 26.689 96.7751 26.5867C96.4332 26.4867 96.1378 26.3332 95.889 26.1262C95.6401 25.9192 95.4448 25.6588 95.3029 25.3401C95.161 25.0238 95.0843 24.6494 95.0703 24.2168V24.0912V24.0959ZM97.917 22.1632C97.596 22.1632 97.3053 22.2051 97.0425 22.2888C96.7797 22.3725 96.5565 22.4981 96.3727 22.6632C96.189 22.8283 96.0471 23.0353 95.9471 23.2842C95.8471 23.533 95.7959 23.8261 95.7959 24.1587C95.7959 24.4912 95.8448 24.7633 95.9471 25.0075C96.0471 25.2518 96.189 25.4564 96.3727 25.6215C96.5565 25.7867 96.7797 25.9123 97.0425 25.9983C97.3053 26.0844 97.596 26.1262 97.917 26.1262C98.2379 26.1262 98.5287 26.0844 98.7915 26.0006C99.0543 25.9169 99.2775 25.7913 99.4613 25.6262C99.645 25.4611 99.7869 25.2564 99.8869 25.0122C99.9869 24.768 100.036 24.4819 100.036 24.1563C100.036 23.8307 99.9869 23.5516 99.8869 23.3051C99.7869 23.0586 99.645 22.8493 99.4613 22.6795C99.2775 22.5097 99.0543 22.3795 98.7915 22.2911C98.5287 22.2027 98.2379 22.1585 97.917 22.1585V22.1632Z" fill="currentColor"/><path d="M107.554 24.6242C107.526 25.3545 107.314 25.8894 106.919 26.2243C106.523 26.5616 105.94 26.729 105.167 26.729C104.395 26.729 103.812 26.5592 103.414 26.215C103.016 25.8731 102.805 25.3429 102.781 24.6219V21.7055H103.465V24.301C103.465 24.5661 103.484 24.8103 103.521 25.0336C103.558 25.2568 103.637 25.4499 103.753 25.6103C103.872 25.7708 104.042 25.8964 104.263 25.9825C104.484 26.0708 104.784 26.115 105.16 26.115C105.537 26.115 105.84 26.0732 106.063 25.9871C106.286 25.9011 106.461 25.7778 106.579 25.6196C106.698 25.4615 106.775 25.2708 106.814 25.0498C106.851 24.8289 106.87 24.5847 106.87 24.3126V21.7031H107.554V24.6196V24.6242Z" fill="currentColor"/><path d="M110.543 24.801V26.5871H109.859V21.7031H113.097C113.353 21.7031 113.585 21.738 113.794 21.8031C114.004 21.8706 114.183 21.9706 114.327 22.1031C114.474 22.2357 114.588 22.4032 114.669 22.6032C114.75 22.8032 114.79 23.0358 114.79 23.3009C114.781 23.5567 114.734 23.78 114.65 23.9707C114.567 24.1614 114.45 24.3172 114.304 24.4382C114.157 24.5591 113.983 24.6498 113.778 24.7103C113.574 24.7708 113.346 24.801 113.094 24.801H110.541H110.543ZM110.543 24.187H113.069C113.371 24.187 113.613 24.1079 113.794 23.9498C113.976 23.7916 114.067 23.5614 114.067 23.259C114.067 22.9567 113.976 22.7288 113.794 22.5636C113.613 22.3985 113.371 22.3148 113.069 22.3148H110.543V24.1847V24.187Z" fill="currentColor"/><path d="M3.47226 4.24281L13.3833 0.63547C18.8536-1.35553 24.9108 1.46899 26.9018 6.93921L7.0796 14.1539C4.34339 15.1498 1.31479 13.7375 0.318896 11.0013C-0.676204 8.26731 0.738242 5.23791 3.47226 4.24281Z" fill="currentColor"/><path d="M23.8629 24.4425L13.9518 28.0498C8.48159 30.0408 2.42438 27.2163 0.433387 21.7461L20.2555 14.5314C22.9917 13.5355 26.0203 14.9478 27.0162 17.684C28.0121 20.4202 26.5999 23.4488 23.8637 24.4447L23.8629 24.4425Z" fill="#772885"/></svg>
            </a>

            <div class="header__menu menu">
                <div class="menu__head">
                    <button type="button" class="menu__close" data-menu-burger="">
                        <svg width="19" height="19" viewbox="0 0 19 19" fill="none"><rect width="24" height="2" rx="1" transform="matrix(0.705635 0.708575 -0.705635 0.708575 1.37695 0)" fill="currentColor"/><rect width="24" height="2" rx="1" transform="matrix(0.705635 -0.708575 0.705635 0.708575 0.402344 17.6169)" fill="currentColor"/></svg>
                    </button>
                </div>
                <nav class="menu__body">
                    <ul class="menu__list">
                        <?php foreach ($nav_header as $item): ?>
                        <li class="menu__item<?= $item['has_mega'] ? ' menu__item--dropdown' : '' ?>">
                            <a href="<?= e($item['url']) ?>" class="menu__link"<?= $item['has_mega'] ? ' data-menu="mega-' . $item['id'] . '"' : '' ?>>
                                <span class="menu__link-text"><?= e($item['title']) ?></span>
                            </a>
                            <?php if ($item['has_mega'] && !empty($item['mega_categories'])): ?>
                            <div class="submenu" data-fls-lp="" data-menu-target="mega-<?= $item['id'] ?>" data-submenu="">
                                <div class="submenu__container">
                                    <div class="submenu__inner">

                                    <?php if (($item['mega_type'] ?? 'solutions') === 'departments'): ?>
                                    <!-- Departments-type: flat grid with photo -->
                                    <?php
                                    // Collect all subitems across all categories
                                    $all_dept_items = [];
                                    foreach ($item['mega_categories'] as $cat) {
                                        foreach ($cat['subitems'] as $sub) $all_dept_items[] = $sub;
                                    }
                                    $col_size  = 5;
                                    $columns   = array_chunk($all_dept_items, $col_size);
                                    $img_text  = $item['mega_img_text'] ?? 'Crafting innovative apps with a focus on user experience and scalability.';
                                    ?>
                                    <div class="submenu__wrapper">
                                        <div class="submenu__info">
                                            <?php foreach ($columns as $col): ?>
                                            <ul class="submenu__info-list">
                                                <?php foreach ($col as $sub): ?>
                                                <li class="submenu__info-item">
                                                    <a href="<?= e($sub['url']) ?>" class="submenu__info-link">
                                                        <?php if ($sub['icon_svg']): ?>
                                                        <span class="submenu__info-icon"><?= $sub['icon_svg'] ?></span>
                                                        <?php endif; ?>
                                                        <span class="submenu__info-title"><?= e($sub['title']) ?></span>
                                                    </a>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="submenu__img">
                                            <img alt="Image" loading="lazy" src="./assets/img/header/image.webp">
                                            <div class="submenu__img-text"><?= e($img_text) ?></div>
                                        </div>
                                    </div>

                                    <?php else: ?>
                                    <!-- Solutions/Industries-type: category list + subitems -->
                                        <ul class="submenu__list">
                                            <?php foreach ($item['mega_categories'] as $ci => $cat): ?>
                                            <li class="submenu__item">
                                                <a href="#" class="submenu__link" data-submenu-link="<?= $ci + 1 ?>">
                                                    <span class="submenu__link-title"><?= e($cat['title']) ?></span>
                                                    <span class="submenu__link-text"><?= e($cat['description']) ?></span>
                                                </a>
                                                <div class="submenu__body" data-submenu-item="<?= $ci + 1 ?>">
                                                    <ul class="submenu__sublist" data-scroll-block="">
                                                        <?php foreach ($cat['subitems'] as $sub): ?>
                                                        <li class="submenu__subitem">
                                                            <a href="<?= e($sub['url']) ?>" class="submenu__sublink">
                                                                <?php if ($sub['icon_svg']): ?>
                                                                <span class="submenu__sublink-icon"><?= $sub['icon_svg'] ?></span>
                                                                <?php endif; ?>
                                                                <span class="submenu__sublink-title"><?= e($sub['title']) ?></span>
                                                                <span class="submenu__sublink-text"><?= e($sub['description']) ?></span>
                                                            </a>
                                                        </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                            <?php elseif (!empty($item['children'])): ?>
                            <ul class="menu__dropdown">
                                <?php foreach ($item['children'] as $child): ?>
                                <li><a href="<?= e($child['url']) ?>"><?= e($child['title']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Language switcher -->
                    <?php if (count($languages) > 1): ?>
                    <div class="menu__lang lang-switcher">
                        <?php foreach ($languages as $l): ?>
                        <a href="<?= e((int)$l['is_default'] ? '/' : '/' . $l['code'] . '/') ?>"
                           class="lang-switcher__item<?= $l['code'] === $lang_code ? ' lang-switcher__item--active' : '' ?>">
                            <?= e(strtoupper($l['code'])) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="header__actions">
                <a href="#getintouch" class="header__btn button button--secondary">Get in touch</a>
                <button type="button" class="header__burger" data-menu-burger="">
                    <svg width="25" height="14" viewbox="0 0 25 14" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="25" height="1.99897" rx="0.999485" transform="rotate(0.0330802)" fill="currentColor"/><rect y="6" width="25" height="2" rx="1" fill="currentColor"/><rect y="12" width="25" height="2" rx="1" fill="currentColor"/></svg>
                </button>
            </div>
        </div>
    </div>
</header>
<!-- ════════════════════════════ /HEADER ════════════════════════════════ -->
