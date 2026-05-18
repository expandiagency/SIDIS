<?php
require_once __DIR__ . '/includes/functions.php';

$pdo = db();
$lang_id = 1;

$posts = [
    [
        'slug'   => 'automate-repetitive-tasks-with-rpa',
        'title'  => 'Automate Repetitive Tasks with Robotic Process Automation',
        'excerpt'=> 'Discover how RPA helps SMEs replace manual, time-consuming processes with intelligent automation, reducing errors and saving valuable time.',
        'content'=> '<h2 id="some-section-1">How AI Delays Compound Costs</h2><p>Every day that manual processes continue unchecked, costs accumulate silently. Repetitive data entry, invoice processing, and report generation consume thousands of hours each year that could be redirected to strategic work.</p><h2 id="some-section-2">What RPA Can Do for Your Business</h2><p>Robotic Process Automation (RPA) replicates human actions on digital systems — clicking, typing, copying — but at machine speed and without errors. From auditing workflows to integrating RPA with your existing systems, we streamline operations, reduce errors, and save your team valuable time and costs.</p><h2 id="some-section-3">Implementation Roadmap</h2><p>A typical RPA implementation follows four phases: Discovery, Design, Development, and Deployment. Each phase is time-boxed and includes client reviews to ensure alignment with business goals.</p><h2 id="some-section-4">ROI You Can Measure</h2><p>Our clients consistently report 30–50% reduction in processing time within the first 90 days. That translates directly to cost savings and employee satisfaction as teams focus on higher-value work.</p>',
        'date'   => '2026-03-24 09:00:00',
        'image'  => './assets/img/blog/image-4.webp',
    ],
    [
        'slug'   => 'crm-integration-guide-for-smbs',
        'title'  => 'CRM Integration Guide for Small and Medium Businesses',
        'excerpt'=> 'Learn how connecting your CRM with other business tools eliminates data silos, improves customer relationships, and drives revenue growth.',
        'content'=> '<h2 id="some-section-1">Why CRM Integration Matters</h2><p>A CRM sitting alone is just a contact database. When connected to your email, support desk, billing, and marketing tools, it becomes the central nervous system of your business — giving every team a shared view of the customer.</p><h2 id="some-section-2">Common Integration Points</h2><p>The most impactful integrations connect CRM to: email marketing platforms, customer support tickets, e-commerce order data, accounting software, and communication tools like Slack or Teams.</p><h2 id="some-section-3">Step-by-Step Approach</h2><p>We start with a discovery workshop to map your current tools and data flows. Then we design the integration architecture, build connectors, and test thoroughly before go-live. Post-launch support ensures everything keeps running smoothly.</p><h2 id="some-section-4">Measuring Success</h2><p>Key metrics include: reduction in manual data entry hours, improvement in lead response time, and increase in customer satisfaction scores. Most clients see measurable ROI within the first quarter.</p>',
        'date'   => '2026-04-05 10:00:00',
        'image'  => './assets/img/blog/image-3.webp',
    ],
    [
        'slug'   => 'ai-chatbots-for-customer-support',
        'title'  => 'AI Chatbots: Transforming Customer Support in 2026',
        'excerpt'=> 'AI-powered chatbots now handle 60–80% of routine customer queries without human intervention. Here is how to deploy them effectively.',
        'content'=> '<h2 id="some-section-1">The State of AI Customer Support</h2><p>Modern AI chatbots go far beyond scripted FAQ responses. Powered by large language models, they understand context, handle complex queries, escalate to humans when needed, and learn from every interaction.</p><h2 id="some-section-2">Key Use Cases</h2><p>The highest ROI use cases include: order status inquiries, appointment booking, product recommendations, troubleshooting guides, and initial qualification of sales leads.</p><h2 id="some-section-3">Integration with Existing Systems</h2><p>Effective chatbots connect to your CRM, ticketing system, and product database in real time. This allows them to give personalised answers based on the specific customer\'s history and current situation.</p><h2 id="some-section-4">Deployment Best Practices</h2><p>Start with the top 10 most common queries. Build a robust handoff to human agents for complex cases. Monitor CSAT scores weekly and iterate. Avoid deploying without a clear escalation path.</p>',
        'date'   => '2026-04-18 11:00:00',
        'image'  => './assets/img/blog/image-2.webp',
    ],
    [
        'slug'   => 'erp-implementation-lessons-learned',
        'title'  => 'ERP Implementation: Lessons Learned from 50+ Projects',
        'excerpt'=> 'After implementing ERP systems across 50+ companies, we have identified the patterns that separate successful rollouts from expensive failures.',
        'content'=> '<h2 id="some-section-1">Why ERP Projects Fail</h2><p>Research consistently shows that 50–75% of ERP projects run over budget or over schedule. The root causes are rarely technical — they are organisational. Poor change management, unclear requirements, and lack of executive sponsorship top the list.</p><h2 id="some-section-2">The Discovery Phase</h2><p>The single most important investment is a thorough discovery phase. This means interviewing every department, documenting current workflows, and identifying the gap between what the system can do and what the business actually needs.</p><h2 id="some-section-3">Phased Rollout Strategy</h2><p>Big-bang ERP launches (all modules at once) dramatically increase risk. We recommend a phased approach: start with finance and inventory, stabilise, then add manufacturing, HR, and CRM modules in successive waves.</p><h2 id="some-section-4">Post Go-Live Support</h2><p>The 90 days after go-live are critical. Dedicate resources to super-user training, rapid bug fixing, and report customisation. Plan for a productivity dip of 15–20% in the first month — it is normal and temporary.</p>',
        'date'   => '2026-05-01 09:30:00',
        'image'  => './assets/img/blog/image-1.webp',
    ],
];

$inserted = 0;
foreach ($posts as $p) {
    $existing = row('SELECT id FROM posts WHERE slug=?', [$p['slug']]);
    if ($existing) continue;

    $post_id = insert('posts', [
        'slug'       => $p['slug'],
        'is_active'  => 1,
        'published_at' => $p['date'],
    ]);

    insert('posts_t', [
        'post_id'   => $post_id,
        'lang_id'   => $lang_id,
        'title'     => $p['title'],
        'subtitle'  => '',
        'excerpt'   => $p['excerpt'],
        'content'   => $p['content'],
        'meta_title'       => $p['title'],
        'meta_description' => $p['excerpt'],
    ]);

    $inserted++;
}

echo "<p>Done! $inserted posts created.</p>";
echo "<p><a href='/blog/'>View Blog</a> · <a href='/admin/'>Go to Admin</a></p>";

// Self-delete
unlink(__FILE__);
echo "<p style='color:gray;font-size:12px'>This script has been deleted.</p>";
