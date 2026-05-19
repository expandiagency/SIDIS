<?php
/**
 * Seed all case studies with full content.
 * Visit: https://sidis.expandi.agency/run_cases_seed.php
 * Self-deletes after run.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes/functions.php';

// Migrate extras column if not exists
try { db()->exec("ALTER TABLE cases_t ADD COLUMN extras MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}

echo '<pre>';
$lang_id = 1;

$default_challenges = [
    ['title'=>'Inefficient Legacy Systems',        'text'=>'Outdated systems slow operations, increase errors, and raise costs. SIDIS integrates modern solutions to streamline processes and boost productivity.'],
    ['title'=>'Data Silos and Lack of Integration','text'=>'Disconnected systems hinder data flow and decision-making. We unify your tech stack for seamless data sharing and enhanced insights.'],
    ['title'=>'Scalability Challenges',             'text'=>'Growing businesses need flexible, scalable solutions. SIDIS provides adaptable systems that evolve with your changing needs.'],
    ['title'=>'Limited Automation',                 'text'=>'Manual processes drain resources and limit growth. We automate key workflows to free up your team and drive innovation.'],
];

// case_tech_items table: case_id, lang_id, name, icon_svg, sort_order (NO description column)
// descriptions are stored in extras.tech_items[]
$default_tech_names = [
    "Backend\nDevelopment",
    "Frontend\nDevelopment",
    "Database\nManagement",
    "Cloud\nInfrastructure",
    "AI &\nAutomation",
];

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

$all_cases = rows('SELECT id, slug FROM cases WHERE is_active=1');

foreach ($all_cases as $c) {
    $case_id = (int)$c['id'];
    $slug    = $c['slug'];
    echo "\n--- $slug (id=$case_id) ---\n";

    $ct = row('SELECT id, overview_text, extras FROM cases_t WHERE case_id=? AND lang_id=?', [$case_id, $lang_id]);
    if (!$ct) { echo "No cases_t row — skipping.\n"; continue; }

    $updates = [];
    if (empty($ct['overview_text'])) {
        $updates['overview_text'] = 'SIDIS empowers SMEs by transitioning them from outdated manual processes to advanced automation. We specialize in streamlining workflows, integrating AI-driven tools, and optimizing existing systems to boost efficiency, minimize errors, and significantly reduce operational costs.';
        echo "Overview text set.\n";
    }

    if (empty($ct['extras'])) {
        $updates['extras'] = json_encode($default_extras, JSON_UNESCAPED_UNICODE);
        echo "Extras saved.\n";
    } else {
        // Update extras to ensure tech_items with descriptions are present
        $ex = json_decode($ct['extras'], true) ?: [];
        if (empty($ex['tech_items'])) {
            $ex['tech_items'] = $default_extras['tech_items'];
            $updates['extras'] = json_encode($ex, JSON_UNESCAPED_UNICODE);
            echo "Extras tech_items updated.\n";
        } else {
            echo "Extras already set.\n";
        }
    }

    if ($updates) {
        update('cases_t', $updates, ['id' => $ct['id']]);
    }

    // Seed challenges if none
    $ch_count = (int)row('SELECT COUNT(*) as c FROM case_challenges WHERE case_id=?', [$case_id])['c'];
    if ($ch_count === 0) {
        foreach ($default_challenges as $i => $ch) {
            $ch_id = insert('case_challenges', ['case_id'=>$case_id,'sort_order'=>$i]);
            insert('case_challenges_t', ['challenge_id'=>$ch_id,'lang_id'=>$lang_id,'title'=>$ch['title'],'text'=>$ch['text']]);
        }
        echo "Challenges seeded.\n";
    } else {
        echo "Challenges already exist ($ch_count).\n";
    }

    // Seed tech items if none (only valid columns: case_id, lang_id, name, icon_svg, sort_order)
    $tech_count = (int)row('SELECT COUNT(*) as c FROM case_tech_items WHERE case_id=?', [$case_id])['c'];
    if ($tech_count === 0) {
        foreach ($default_tech_names as $i => $name) {
            insert('case_tech_items', ['case_id'=>$case_id,'lang_id'=>$lang_id,'name'=>$name,'icon_svg'=>'','sort_order'=>$i]);
        }
        echo "Tech items seeded.\n";
    } else {
        echo "Tech items already exist ($tech_count).\n";
    }

    // Seed services if none
    $svc_count = (int)row('SELECT COUNT(*) as c FROM case_services WHERE case_id=? AND lang_id=?', [$case_id,$lang_id])['c'];
    if ($svc_count === 0) {
        $services = ['Workflow Automation','Process Automation','CRM Integration','AI Implementation','Data Analytics'];
        foreach ($services as $i => $s) {
            insert('case_services', ['case_id'=>$case_id,'lang_id'=>$lang_id,'service_name'=>$s,'sort_order'=>$i]);
        }
        echo "Services seeded.\n";
    } else {
        echo "Services already exist ($svc_count).\n";
    }
}

echo "\nAll cases patched!\n";
echo '</pre>';

unlink(__FILE__);
echo '<p style="color:green;font-family:monospace">Script self-deleted. All cases ready!</p>';
