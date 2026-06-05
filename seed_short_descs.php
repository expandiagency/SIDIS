<?php
// Updates solution_pages_t.description (~160 chars) for all 46 solution/department/industry pages
// Trigger: /seed_short_descs.php?key=sidis2026
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }
require_once __DIR__ . '/includes/functions.php';

$lang_id = 1;
$dry     = isset($_GET['dry']);

$data = [
    // ── SOLUTIONS ──────────────────────────────────────────────────────────────
    'business-process-analysis-optimization' => 'Map, analyse, and redesign your core business processes to eliminate waste, reduce cycle times, and build a foundation ready for automation and sustainable scale.',
    'business-process-automation'           => 'Automate repetitive workflows — approvals, data entry, and notifications — with connected systems that eliminate bottlenecks and run reliably across your operations.',
    'sales-automation'                      => 'Automate CRM updates, lead routing, follow-up sequences, and pipeline reports so your reps focus on closing deals instead of administrative tasks that slow them down.',
    'marketing-automation'                  => 'Build systems that handle lead capture, nurturing sequences, segmentation, and campaign triggers — delivering the right message automatically at the right time.',
    'customer-support-automation'           => 'Automate ticket routing, first-response handling, and status updates so your support team focuses on complex issues and handles higher volume without adding headcount.',
    'operations-automation'                 => 'Replace manual handoffs and fragile tracking with automated workflows that run without supervision — making operations predictable, scalable, and ready to handle growth.',
    'ai-chatbots-for-business'              => 'Deploy AI chatbots that qualify leads, handle support, book meetings, and guide users through complex flows — 24/7, integrated with your existing systems.',
    'ai-implementation-for-business'        => 'Identify where AI fits your workflows and implement practical solutions — document processing, decision support, and predictive automation built around real operations.',
    'custom-crm-development'                => 'A CRM built for how you actually sell — not for everyone. Full ownership, no per-seat fees, and a system that fits your exact process and evolves with your business.',
    'custom-erp-development'                => 'An ERP built around how your company operates — connecting inventory, finance, and operations in one tailored platform with no unnecessary modules or vendor lock-in.',
    'data-reporting-automation'             => 'Automate data pipelines and reporting so the right information reaches the right people on time, without the manual pull-and-format routine slowing your decisions.',
    'internal-business-systems-development' => 'Replace spreadsheets and workarounds with purpose-built portals, dashboards, and management tools integrated with your existing stack and designed for your team.',
    'replacing-manual-work-with-automation' => 'Audit your operations and replace predictable manual tasks — data entry, file transfers, copy-pasting — with background automations that free your team for strategic work.',
    'saas-platform-development'             => 'Design and build scalable SaaS platforms from concept to launch — multi-tenancy, subscription management, and automation built in from the first line of code.',
    'scaling-business-through-automation'   => 'Build the automation infrastructure that lets your business handle more volume with the same team — fewer bottlenecks, connected systems, and processes that scale.',
    'systems-api-integrations'              => 'Connect your CRM, ERP, payment systems, and communication tools through custom integrations that eliminate manual data transfers and keep your stack in sync automatically.',
    'unified-business-management-systems'   => 'Replace disconnected tools with a unified platform that brings operations, data, and workflows into one place — custom-built to your structure and integrated with existing systems.',
    'voice-ai-assistants'                   => 'Build custom voice AI that handles inbound calls, qualifies leads, answers questions, and books appointments — 24/7 availability, integrated with your existing systems.',

    // ── DEPARTMENTS ────────────────────────────────────────────────────────────
    'analytics-bi'       => 'Automate analytics and BI workflows — data collection, report generation, and dashboard updates — so your team acts on insights instead of spending time preparing data.',
    'compliance'         => 'Automate document management, approval chains, deadline tracking, and audit trails to enforce consistent compliance process and reduce risk from manual handling.',
    'customer-support'   => 'Automate ticket triage, routing, and first-response handling so your agents focus on complex issues while managing higher volume without adding operational headcount.',
    'executive-management' => 'Surface consolidated reports, performance dashboards, and workflow alerts automatically so leadership has full visibility without waiting on manually compiled updates.',
    'finance'            => 'Automate invoice processing, reconciliation, approvals, and financial reporting — eliminating manual data entry and enforcing consistent process across your finance team.',
    'hr'                 => 'Automate onboarding, document collection, approvals, and employee data management to reduce administrative burden and free HR for strategic, people-focused work.',
    'it-engineering'     => 'Automate infrastructure provisioning, access management, incident workflows, and internal ticketing so engineers focus on technical work instead of operational overhead.',
    'legal'              => 'Automate contract workflows, document routing, deadline tracking, and compliance documentation to reduce turnaround times and free legal professionals for substantive work.',
    'marketing'          => 'Automate campaign setup, lead routing, content scheduling, and performance reporting so your marketing team focuses on strategy and creative rather than manual execution.',
    'operations'         => 'Automate handoffs, vendor management, project coordination, and internal reporting — making operations predictable and scalable without constant manual oversight.',
    'partnerships'       => 'Automate partner onboarding, communication workflows, activity tracking, and reporting so your team focuses on relationship building rather than coordination overhead.',
    'product'            => 'Automate feedback routing, roadmap updates, release communication, and cross-team reporting so your product team spends time on decisions instead of information chasing.',
    'procurement'        => 'Automate vendor onboarding, purchase orders, approval routing, invoice matching, and contract management to reduce errors and accelerate your procurement lifecycle.',
    'recruitment'        => 'Automate interview scheduling, candidate communications, application routing, and document collection so recruiters focus on evaluation and relationship building.',
    'sales'              => 'Automate CRM updates, lead assignment, follow-up sequences, and pipeline reports so your reps focus on selling rather than the administrative work that slows everything down.',

    // ── INDUSTRIES ─────────────────────────────────────────────────────────────
    'affiliate-marketing'          => 'Automate partner onboarding, campaign tracking, commission calculation, and reporting to scale your affiliate program without growing your operations team proportionally.',
    'arbitration-companies'        => 'Automate case intake, document management, scheduling, communications, and compliance tracking to enforce consistency and reduce administrative burden on every case.',
    'retail'                       => 'Automate order processing, inventory updates, fulfillment, and customer notifications to handle more orders at volume without adding operational staff proportionally.',
    'fintech'                      => 'Automate KYC flows, transaction monitoring, compliance reporting, and operational processes to handle high volumes reliably while your team focuses on product and growth.',
    'hr-recruiting'                => 'Automate candidate sourcing, screening, scheduling, and client reporting so your agency scales operations without increasing the administrative headcount required.',
    'investment-management'        => 'Automate portfolio reporting, compliance documentation, client communications, and data aggregation so analysts focus on decisions rather than data assembly.',
    'lead-generation-agencies'     => 'Automate prospect sourcing, outreach sequences, lead qualification, and client delivery to generate more output with the same team at consistent, repeatable quality.',
    'legaltech'                    => 'Automate document processing, client onboarding, product operations, and reporting to build a scalable LegalTech business with strong compliance and operational efficiency.',
    'logistics'                    => 'Automate shipment tracking, documentation, vendor communications, and reporting to reduce errors and maintain real-time visibility across every stage of your supply chain.',
    'marketing-advertising'        => 'Automate client reporting, campaign workflows, approvals, and data aggregation so your agency handles more clients without growing the operations team proportionally.',
    'real-estate'                  => 'Automate lead follow-up, document management, transaction coordination, and CRM updates so your agents focus on deals and client relationships rather than manual admin.',
    'saas-startups'                => 'Automate customer onboarding, billing, support workflows, and internal reporting to scale revenue without scaling headcount at the same rate as your user base grows.',
    'smes-general-growing-businesses' => 'Automate your sales, operations, and reporting workflows to handle more growth without the overhead increase that usually comes with scaling a business manually.',
    'wellness-mental-health'       => 'Automate scheduling, intake forms, follow-up communications, billing, and reporting so practitioners spend more time on client care and less time on administrative tasks.',
];

header('Content-Type: text/plain; charset=utf-8');
echo ($dry ? "=== DRY RUN ===\n\n" : "=== APPLYING ===\n\n");

$ok = $skip = $miss = 0;

foreach ($data as $slug => $desc) {
    $page = row('SELECT id FROM solution_pages WHERE slug=?', [$slug]);
    if (!$page) {
        echo "❌  NOT FOUND slug=\"$slug\"\n";
        $miss++;
        continue;
    }
    $pid = $page['id'];
    $t = row('SELECT id FROM solution_pages_t WHERE page_id=? AND lang_id=?', [$pid, $lang_id]);
    if (!$t) {
        echo "⚠️  No translation row for slug=\"$slug\"\n";
        $skip++;
        continue;
    }
    $len = mb_strlen($desc);
    if (!$dry) {
        update('solution_pages_t', ['description' => $desc], ['id' => $t['id']]);
    }
    echo ($dry ? '[DRY] ' : '') . "✅  $slug ($len chars)\n";
    $ok++;
}

echo "\n---\nUpdated: $ok | Skipped: $skip | Not found: $miss\n";
