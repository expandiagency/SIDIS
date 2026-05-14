<?php
require_once __DIR__ . '/includes/functions.php';

/* ─── Parse URL ─────────────────────────────────────────────────────────── */
$request_uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$request_uri = '/' . trim($request_uri, '/');
$parts = array_values(array_filter(explode('/', $request_uri)));

/* ─── Detect language ───────────────────────────────────────────────────── */
$languages    = get_languages();
$default_lang = get_default_language();
$current_lang = $default_lang;

if (!empty($parts) && !empty(array_filter($languages, fn($l) => $l['code'] === $parts[0]))) {
    $current_lang = get_language($parts[0]);
    array_shift($parts);
}

$lang_id   = (int)($current_lang['id'] ?? 1);
$lang_code = $current_lang['code'] ?? 'en';

/* ─── Route ─────────────────────────────────────────────────────────────── */
$page    = $parts[0] ?? '';
$segment = $parts[1] ?? '';

$template_data = [
    'lang'         => $current_lang,
    'default_lang' => $default_lang,
    'languages'    => $languages,
    'lang_id'      => $lang_id,
    'nav_header'   => get_nav($lang_id, 'header'),
    'nav_footer'   => get_nav($lang_id, 'footer'),
];

switch ($page) {
    case '':
    case 'home':
        $template_data['home']    = get_home($lang_id);
        $template_data['why_slides'] = get_why_slides($lang_id);
        $template_data['partners']   = get_partner_logos();
        $template_data['auto_imgs']  = get_automation_images();
        $template_data['reviews']    = get_reviews($lang_id);
        $template_data['solutions_items'] = [
            'solutions'   => get_solution_items($lang_id, 'solution'),
            'departments' => get_solution_items($lang_id, 'department'),
            'industries'  => get_solution_items($lang_id, 'industry'),
        ];
        $template_data['featured_cases'] = get_cases($lang_id, ['is_featured'=>1], 4);
        if (empty($template_data['featured_cases'])) {
            $template_data['featured_cases'] = get_cases($lang_id, [], 4);
        }
        $template_data['home_blocks'] = rows('SELECT * FROM home_blocks WHERE is_active=1 ORDER BY sort_order');
        $template_data['page_class'] = 'home-page';
        render('home', $template_data);
        break;

    case 'solutions':
    case 'departments':
    case 'industries':
        if ($segment) {
            $sol_page = get_solution_page($lang_id, $page . '/' . $segment);
            if (!$sol_page) $sol_page = get_solution_page($lang_id, $segment);
            if (!$sol_page) { render('404', $template_data); break; }
            $template_data['solution_page'] = $sol_page;
            $template_data['page_class'] = 'solutions-page';
            render('solutions', $template_data);
        } else {
            $sol_page = get_solution_page($lang_id, $page);
            if (!$sol_page) {
                $sol_page = [
                    'title' => ucfirst($page),
                    'type'  => $page,
                    'items' => get_solution_items($lang_id, rtrim($page, 's')),
                ];
            }
            $template_data['solution_page'] = $sol_page;
            $template_data['page_class'] = 'solutions-page';
            render('solutions', $template_data);
        }
        break;

    case 'cases':
        if ($segment) {
            $case = get_case($lang_id, $segment);
            if (!$case) { render('404', $template_data); break; }
            $template_data['case'] = $case;
            $template_data['page_class'] = '';
            render('case', $template_data);
        } else {
            $template_data['cases']   = get_cases($lang_id, $_GET);
            $template_data['terms']   = [
                'solutions'   => get_terms($lang_id, 'solution'),
                'departments' => get_terms($lang_id, 'department'),
                'industries'  => get_terms($lang_id, 'industry'),
            ];
            $template_data['page_class'] = '';
            render('cases', $template_data);
        }
        break;

    case 'blog':
        if ($segment) {
            $post = get_post($lang_id, $segment);
            if (!$post) { render('404', $template_data); break; }
            $template_data['post'] = $post;
            $template_data['page_class'] = '';
            render('article', $template_data);
        } else {
            $template_data['posts'] = get_posts($lang_id, $_GET);
            $template_data['terms'] = [
                'solutions'   => get_terms($lang_id, 'solution'),
                'departments' => get_terms($lang_id, 'department'),
                'industries'  => get_terms($lang_id, 'industry'),
            ];
            $template_data['page_class'] = '';
            render('blog', $template_data);
        }
        break;

    default:
        render('404', $template_data);
        break;
}

/* ─── Renderer ──────────────────────────────────────────────────────────── */
function render(string $tpl, array $data): void {
    extract($data, EXTR_SKIP);
    $path = __DIR__ . '/templates/' . $tpl . '.php';
    if (!file_exists($path)) {
        http_response_code(404);
        $path = __DIR__ . '/templates/404.php';
    }
    require $path;
    exit;
}
