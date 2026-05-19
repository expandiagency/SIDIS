<?php
require_once __DIR__ . '/includes/functions.php';

/* ─── One-time seed trigger: ?run_seed=sidis2026 ───────────────────────── */
if (($_GET['run_seed'] ?? '') === 'sidis2026') {
    try { db()->exec("ALTER TABLE cases_t ADD COLUMN extras MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}
    $lang_id_seed = 1;
    $default_challenges = [
        ['title'=>'Inefficient Legacy Systems',        'text'=>'Outdated systems slow operations, increase errors, and raise costs. SIDIS integrates modern solutions to streamline processes and boost productivity.'],
        ['title'=>'Data Silos and Lack of Integration','text'=>'Disconnected systems hinder data flow and decision-making. We unify your tech stack for seamless data sharing and enhanced insights.'],
        ['title'=>'Scalability Challenges',             'text'=>'Growing businesses need flexible, scalable solutions. SIDIS provides adaptable systems that evolve with your changing needs.'],
        ['title'=>'Limited Automation',                 'text'=>'Manual processes drain resources and limit growth. We automate key workflows to free up your team and drive innovation.'],
    ];
    $default_tech_names = ["Backend\nDevelopment","Frontend\nDevelopment","Database\nManagement","Cloud\nInfrastructure","AI &\nAutomation"];
    $default_extras = [
        'challenges_text'   => 'SIDIS helps SMEs replace manual, time-consuming processes with intelligent automation. From auditing workflows to integrating RPA with your existing systems, we streamline operations, reduce errors, and save your team valuable time and costs.',
        'tech_text'         => 'To effectively automate tasks, our team selected a suite of technologies designed to enhance campaign management, customer engagement, and data analysis.',
        'tech_items'        => [
            ['name'=>"Backend\nDevelopment",  'description'=>'We built a robust, scalable backend using Node.js and PostgreSQL, designed to handle high transaction volumes with minimal latency.'],
            ['name'=>"Frontend\nDevelopment", 'description'=>'React-based frontend delivers a seamless user experience across all devices with real-time data updates.'],
            ['name'=>"Database\nManagement",  'description'=>'PostgreSQL for structured data, Redis for caching — ensuring peak performance and reliability under load.'],
            ['name'=>"Cloud\nInfrastructure", 'description'=>'AWS-hosted infrastructure with auto-scaling, load balancing, and 99.9% uptime SLA.'],
            ['name'=>"AI &\nAutomation",      'description'=>'Machine learning models and RPA workflows automate repetitive tasks and surface actionable insights.'],
        ],
        'solution_subtitle' => 'The Solution',
        'solution_title'    => 'End-to-end automation and digital transformation delivered in parallel',
        'solution_text1'    => '<p>The range of services provided by SIDIS covers full-cycle product development and includes business analysis, product design, software architecture, data science, software development, quality assurance and implementation. Thanks to our ability to adjust the delivery volume quickly, <b>SIDIS has grown the dedicated team from 10 to 50 specialists</b> in just the first three months of our partnership.</p><ul><li>Full workflow audit and automation blueprint delivered in week one.</li><li>Custom RPA bots deployed for invoice processing, data entry, and reporting.</li><li>CRM integration connecting sales, support, and billing into one unified view.</li><li>AI-powered dashboard providing real-time operational insights.</li></ul>',
        'solution_text2'    => '<ul><li>Continuous monitoring and optimisation of all automated workflows post-launch.</li><li>Staff training programme to ensure smooth adoption across all departments.</li><li>Scalable cloud infrastructure enabling rapid expansion without re-engineering.</li></ul><p>The client team appreciated that SIDIS acted as a technology consultant, contributing ideas and challenging requirements rather than following a standard outsourcing model. This approach aligns perfectly with fast-paced, growth-oriented businesses.</p>',
        'solution_image'    => '/assets/img/projects/image-7.webp',
        'results_title'     => 'Final Results',
        'results_text'      => 'The automation programme delivered measurable ROI within the first quarter, with compounding benefits as more workflows were optimised.',
        'results'           => [
            ['title'=>'Agile Legacy Transitions',       'text'=>'Modernized systems improve speed, accuracy, and cut costs. SIDIS seamlessly integrates new solutions to boost your business efficiency.'],
            ['title'=>'Integrated Data Ecosystems',     'text'=>'Unified systems enhance data flow and improve decision-making. We integrate your tech stack for seamless data and better insights.'],
            ['title'=>'Adaptable Scalability Solutions','text'=>'Scalable solutions are vital for growing businesses. SIDIS delivers adaptable systems that evolve with your business needs.'],
            ['title'=>'Advanced Automation Processes',  'text'=>'Automated processes free up resources and drive growth. We automate key workflows so your team can focus on strategic work.'],
        ],
        'results_image'     => '/assets/img/blog/Image-1.webp',
        'result_quote'      => 'Their automation expertise helped us streamline several internal processes. We saw measurable efficiency improvements within weeks.',
        'result_user_name'  => 'Michael Turner',
        'result_user_work'  => 'CEO, Solaris Dynamics',
        'result_user_image' => '/assets/img/reviews/image-1.webp',
        'result_linkedin'   => '#',
    ];
    echo '<pre>';
    $all_seed_cases = rows('SELECT id, slug FROM cases WHERE is_active=1');
    foreach ($all_seed_cases as $sc) {
        $cid = (int)$sc['id'];
        echo "\n--- {$sc['slug']} (id=$cid) ---\n";
        $ct = row('SELECT id, overview_text, extras FROM cases_t WHERE case_id=? AND lang_id=?', [$cid, $lang_id_seed]);
        if (!$ct) { echo "No cases_t row.\n"; continue; }
        $upd = [];
        if (empty($ct['overview_text'])) { $upd['overview_text'] = 'SIDIS empowers SMEs by transitioning them from outdated manual processes to advanced automation. We specialize in streamlining workflows, integrating AI-driven tools, and optimizing existing systems to boost efficiency, minimize errors, and significantly reduce operational costs.'; echo "Overview set.\n"; }
        if (empty($ct['extras'])) {
            $upd['extras'] = json_encode($default_extras, JSON_UNESCAPED_UNICODE); echo "Extras saved.\n";
        } else {
            $ex2 = json_decode($ct['extras'], true) ?: [];
            if (empty($ex2['tech_items'])) { $ex2['tech_items'] = $default_extras['tech_items']; $upd['extras'] = json_encode($ex2, JSON_UNESCAPED_UNICODE); echo "Extras tech_items updated.\n"; }
            else { echo "Extras OK.\n"; }
        }
        if ($upd) update('cases_t', $upd, ['id'=>$ct['id']]);
        $ch = (int)row('SELECT COUNT(*) as c FROM case_challenges WHERE case_id=?', [$cid])['c'];
        if ($ch === 0) { foreach ($default_challenges as $i=>$ch2) { $chi = insert('case_challenges',['case_id'=>$cid,'sort_order'=>$i]); insert('case_challenges_t',['challenge_id'=>$chi,'lang_id'=>$lang_id_seed,'title'=>$ch2['title'],'text'=>$ch2['text']]); } echo "Challenges seeded.\n"; } else echo "Challenges exist ($ch).\n";
        $ti = (int)row('SELECT COUNT(*) as c FROM case_tech_items WHERE case_id=?', [$cid])['c'];
        if ($ti === 0) { foreach ($default_tech_names as $i=>$tn) { insert('case_tech_items',['case_id'=>$cid,'lang_id'=>$lang_id_seed,'name'=>$tn,'icon_svg'=>'','sort_order'=>$i]); } echo "Tech items seeded.\n"; } else echo "Tech items exist ($ti).\n";
        $sv = (int)row('SELECT COUNT(*) as c FROM case_services WHERE case_id=? AND lang_id=?', [$cid,$lang_id_seed])['c'];
        if ($sv === 0) { foreach (['Workflow Automation','Process Automation','CRM Integration','AI Implementation','Data Analytics'] as $i=>$s) { insert('case_services',['case_id'=>$cid,'lang_id'=>$lang_id_seed,'service_name'=>$s,'sort_order'=>$i]); } echo "Services seeded.\n"; } else echo "Services exist ($sv).\n";
    }
    echo "\nAll cases seeded!\n</pre>";
    echo '<p style="color:green;font-family:monospace">Done! Remove ?run_seed=sidis2026 from URL when finished.</p>';
    exit;
}

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
        $template_data['home_blocks']  = rows('SELECT * FROM home_blocks WHERE is_active=1 ORDER BY sort_order');
        $template_data['recent_posts'] = get_posts($lang_id, [], 4);
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
            $template_data['solution_page']   = $sol_page;
            $template_data['sol_page_blocks'] = get_sol_page_blocks((int)$sol_page['id'], $lang_id);
            $template_data['reviews']         = get_reviews($lang_id);
            $template_data['recent_posts']    = get_posts($lang_id, [], 4);
            $template_data['featured_cases']  = get_cases($lang_id, [], 4);
            $template_data['page_class']      = 'solutions-page';
            render('solutions', $template_data);
        } else {
            $sol_page = get_solution_page($lang_id, $page);
            if (!$sol_page) {
                $sol_page = ['id'=>0,'title' => ucfirst($page), 'type' => $page, 'items' => get_solution_items($lang_id, rtrim($page, 's'))];
            }
            $template_data['solution_page']   = $sol_page;
            $template_data['sol_page_blocks'] = !empty($sol_page['id']) ? get_sol_page_blocks((int)$sol_page['id'], $lang_id) : [];
            $template_data['reviews']         = get_reviews($lang_id);
            $template_data['recent_posts']    = get_posts($lang_id, [], 4);
            $template_data['featured_cases']  = get_cases($lang_id, [], 4);
            $template_data['page_class']      = 'solutions-page';
            render('solutions', $template_data);
        }
        break;

    case 'cases':
        if ($segment) {
            $case = get_case($lang_id, $segment);
            if (!$case) { render('404', $template_data); break; }
            $template_data['case'] = $case;
            // Load related cases (all other active cases, exclude current)
            $all_cases = get_cases($lang_id, [], 10);
            $template_data['related_cases'] = array_values(array_filter($all_cases, function($c) use ($case) {
                return (int)$c['id'] !== (int)$case['id'];
            }));
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
            // Load related posts if extras defines related_post_ids
            $related_post_ids = isset($post['extras']['related_post_ids']) ? array_map('intval', (array)$post['extras']['related_post_ids']) : [];
            if (!empty($related_post_ids)) {
                $all_posts = get_posts($lang_id, [], 100, 0);
                $related_posts = [];
                foreach ($all_posts as $rp) {
                    if (in_array((int)$rp['id'], $related_post_ids) && (int)$rp['id'] !== (int)$post['id']) {
                        $related_posts[] = $rp;
                    }
                }
                $template_data['related_posts'] = $related_posts;
            } else {
                $template_data['related_posts'] = [];
            }
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
