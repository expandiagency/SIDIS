<?php
// Trigger: /seed_descriptions.php?key=sidis2026
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }
require_once __DIR__ . '/includes/functions.php';

$lang_id = 1;
$dry     = isset($_GET['dry']);

// slug => [title, description]
$data = [

    // ── SOLUTIONS ──────────────────────────────────────────────────────────────
    'business-process-automation' => [
        'Business Process Automation',
        'Repetitive manual tasks cost your business more than you realize — in time, errors, and team capacity. We map your existing workflows and replace them with automated systems that run reliably, connect your tools, and eliminate bottlenecks at every stage. Whether it\'s approvals, data entry, notifications, or reporting — we build automations that fit your exact process. Less manual work, more consistent results, faster operations across the board.',
    ],
    'sales-automation' => [
        'Sales Automation',
        'Your sales team shouldn\'t spend half their day on admin. We automate the repetitive parts of your sales process — lead routing, follow-up sequences, CRM updates, pipeline management, and reporting — so reps stay focused on closing deals. From first touch to signed contract, we build systems that move prospects through your funnel faster with fewer things falling through the cracks. More pipeline activity, less administrative overhead holding your team back.',
    ],
    'marketing-automation' => [
        'Marketing Automation',
        'Running campaigns manually doesn\'t scale. We build marketing automation systems that handle lead capture, nurturing sequences, segmentation, and campaign triggers without manual effort from your team. Your leads get the right message at the right time based on their behavior — automatically. Whether you\'re running email, ads, or multi-channel campaigns, we connect and automate your stack to generate more results from the same budget and headcount.',
    ],
    'customer-support-automation' => [
        'Customer Support Automation',
        'Slow response times and repetitive ticket handling drain your support team and frustrate customers. We build automation systems that handle first-line responses, route tickets to the right agents, resolve common requests without human input, and give your team full context before they respond. The result is faster resolution times, higher customer satisfaction, and a support operation that scales without proportional headcount growth every time volume increases.',
    ],
    'operations-automation' => [
        'Operations Automation',
        'Operational inefficiency hides in manual handoffs, spreadsheet tracking, and processes that only work because someone checks them every day. We identify those friction points and replace them with automated workflows that run without supervision. Approvals move on their own. Data stays in sync. Tasks get assigned automatically. Your operations become reliable, predictable, and ready to handle growth without adding overhead to keep everything running smoothly.',
    ],
    'ai-chatbots-for-business' => [
        'AI Chatbots for Business',
        'A well-built AI chatbot does more than answer FAQs — it qualifies leads, handles support requests, books meetings, and guides users through complex processes without a human in the loop. We design and deploy business chatbots that understand your context, integrate with your existing systems, and handle real conversations at scale. Available 24/7, consistent across every interaction, and built to solve actual business problems rather than just look impressive on your website.',
    ],
    'ai-implementation-for-business' => [
        'AI Implementation for Business',
        'AI creates real value only when implemented around your specific workflows — not dropped in as a generic tool. We help you identify where AI fits your operations, then build and integrate the right solutions: intelligent document processing, predictive automation, decision support, and more. No hype, no vendor lock-in. We focus on practical AI that solves real problems, delivers measurable results, and works the way your team actually operates day to day.',
    ],
    'custom-crm-development' => [
        'Custom CRM Development',
        'Off-the-shelf CRMs are built for everyone, which means they\'re perfect for no one. If your team works around your CRM instead of with it, you need a system built for how you actually sell. We develop custom CRM solutions that match your exact sales process, automate pipeline management, and integrate with every tool your team uses. Full ownership, no per-seat fees growing with your headcount, and a system that evolves as your business does.',
    ],
    'custom-erp-development' => [
        'Custom ERP Development',
        'Generic ERP platforms often require you to adapt your business to fit the software. We flip that model — building ERP systems designed around how your company actually operates. From inventory and procurement to finance and operations, your custom ERP connects every department in one platform tailored to your workflows. No unnecessary modules, no workarounds, no vendor lock-in. Just a system that fits your business precisely and scales alongside it.',
    ],
    'data-reporting-automation' => [
        'Data & Reporting Automation',
        'Manually pulling data from multiple systems and building reports in spreadsheets is slow, error-prone, and delays decisions. We automate your data collection, transformation, and reporting pipelines so the right information reaches the right people automatically — in real time or on schedule. Custom dashboards, automated reports, and integrated analytics give your team clarity without the weekly manual scramble. Better data, faster decisions, and significantly less time wasted on preparation.',
    ],
    'internal-business-systems-development' => [
        'Internal Business Systems Development',
        'Most growing businesses run on a mix of workarounds, spreadsheets, and tools not designed for how they operate today. We build custom internal systems — portals, dashboards, workflow tools, and management platforms — that replace patchwork solutions with something reliable and purpose-built. Designed around your team\'s actual needs, integrated with your existing stack, and built to scale as your business grows and your processes become more complex and demanding.',
    ],
    'replacing-manual-work-with-automation' => [
        'Replacing Manual Work with Automation',
        'If your team spends hours each week on tasks that follow a predictable pattern — data entry, file transfers, status updates, copy-pasting between systems — those are prime targets for automation. We audit your operations, identify the highest-impact manual tasks, and replace them with workflows that run reliably in the background. Less time on repetitive work means more capacity for strategic tasks, fewer human errors, and a team ready to scale.',
    ],
    'saas-platform-development' => [
        'SaaS Platform Development',
        'Building a SaaS product requires more than good code — it requires a team that understands scalable architecture, user experience, and the business model behind the software. We design and develop SaaS platforms from concept to launch: multi-tenancy, subscription management, user roles, and the automation layer that makes your product efficient to operate and easy to scale. Whether it\'s your first product or a complete rebuild, we build for scale from day one.',
    ],
    'scaling-business-through-automation' => [
        'Scaling Business Through Automation',
        'Growth creates operational pressure — more customers, more processes, more complexity. Without automation, scaling means hiring proportionally and watching margins shrink. We build the automation infrastructure that lets your business handle more volume with the same core team. Every process optimized, every repetitive task removed, every system connected. Automation isn\'t just an efficiency tool — it\'s the operational foundation your next stage of growth is built on.',
    ],
    'systems-api-integrations' => [
        'Systems & API Integrations',
        'Disconnected tools create data silos, manual handoffs, and processes that break every time something changes. We connect your software stack through custom API integrations and middleware — making your CRM, ERP, payment systems, communication tools, and data sources work together seamlessly. No more copy-pasting between platforms or waiting for syncs that fail silently. One connected system where data flows automatically to where it needs to go, when it needs to be there.',
    ],
    'unified-business-management-systems' => [
        'Unified Business Management Systems',
        'Running your business across a dozen disconnected tools creates friction, blind spots, and wasted time across every department. We build unified management platforms that bring operations, data, workflows, and teams into a single system designed around how your business works. Finance, projects, clients, HR, and reporting — managed in one place with full visibility across the organization. Custom-built to your structure, integrated with existing tools, and built to scale.',
    ],
    'voice-ai-assistants' => [
        'Voice AI Assistants',
        'Voice AI is a practical tool for handling customer interactions, internal workflows, and support at scale — not just a novelty. We build custom voice AI assistants that handle inbound calls, qualify leads, answer questions, book appointments, and escalate to humans when needed. Designed for your specific use cases, integrated with your systems, and available around the clock. Reduce pressure on your team while delivering consistent, fast responses to every caller.',
    ],

    // ── DEPARTMENTS ────────────────────────────────────────────────────────────
    'analytics-bi' => [
        'Analytics & BI Automation',
        'Your data is only valuable when the right people have it at the right time. We automate analytics and BI workflows — from data collection and transformation to report generation and dashboard updates — so your team makes decisions instead of preparing spreadsheets. Custom dashboards, automated pipelines, and integrated reporting give every department real-time visibility into performance without the manual work that usually sits between raw data and actionable insight.',
    ],
    'compliance' => [
        'Compliance Process Automation',
        'Compliance work is repetitive, documentation-heavy, and high-stakes — a combination where manual processes create real risk. Missed deadlines, inconsistent records, and audit bottlenecks are all symptoms of a process that needs automation. We build systems that handle document management, approval chains, deadline tracking, and audit trails automatically. Your team gets consistent process enforcement, reduced exposure to human error, and audit preparation that no longer requires a team scramble.',
    ],
    'customer-support' => [
        'Customer Support Automation',
        'Support teams spend significant time on repetitive work — answering the same questions, routing tickets, and updating statuses manually. We automate the routine parts of customer support so your agents focus on complex issues that genuinely require a human. Automated triage, smart routing, first-response handling, and resolution tracking help your team handle more volume without sacrificing quality, burning out, or needing to grow headcount every time demand increases.',
    ],
    'executive-management' => [
        'Executive & Management Automation',
        'Senior leaders lose hours every week waiting for reports, chasing status updates, and sitting in meetings that could have been a dashboard. We build management automation systems that surface the right information automatically — consolidated reporting, performance dashboards, workflow alerts, and decision-support tools that give leadership full visibility without manual overhead. Spend less time gathering information and more time acting on it where it matters most.',
    ],
    'finance' => [
        'Finance Process Automation',
        'Finance teams handle high-volume, detail-intensive work where errors carry real consequences. We automate the repetitive parts of your finance operations — invoice processing, reconciliation, expense management, approval workflows, and financial reporting. Custom systems connecting your accounting tools, eliminating manual data entry, and enforcing process consistency across your team. Faster closes, cleaner books, and a finance function that scales with your business without proportional team growth.',
    ],
    'hr' => [
        'HR Process Automation',
        'HR teams manage dozens of processes ripe for automation — onboarding, offboarding, document collection, policy acknowledgments, time-off approvals, and employee data management. We build HR automation systems that reduce administrative burden, ensure consistency at every employee touchpoint, and integrate with your existing HRIS and payroll tools. Your HR team spends less time on paperwork and more time on culture, hiring strategy, and the people work that drives the business forward.',
    ],
    'it-engineering' => [
        'IT & Engineering Automation',
        'IT teams are often the last to benefit from automation despite building it for everyone else. We help IT and engineering departments automate their own operations — infrastructure provisioning, incident response workflows, access management, deployment pipelines, and internal ticketing. Less manual overhead for your technical team, faster response times, and systems that handle routine operational tasks in the background so your engineers focus on work that requires their expertise.',
    ],
    'legal' => [
        'Legal Department Automation',
        'Legal teams handle high volumes of document-intensive work with strict accuracy requirements and tight deadlines. We automate the operational side of your legal department — contract management workflows, document routing, deadline tracking, approval chains, and compliance documentation. Systems that reduce turnaround times, enforce consistent process, and free your legal professionals to focus on substantive work rather than the administrative overhead that takes up too much of their day.',
    ],
    'marketing' => [
        'Marketing Department Automation',
        'Marketing teams manage complex multi-channel operations that generate significant repetitive work — campaign setup, lead routing, content scheduling, performance reporting, and tool synchronization. We automate your marketing workflows so your team focuses on strategy and creative work rather than manual execution. Connected systems that move leads through the right sequences, keep your stack in sync, and surface performance data automatically without the manual weekly pull that nobody enjoys.',
    ],
    'operations' => [
        'Operations Department Automation',
        'Operations is where inefficiency accumulates — manual handoffs, disconnected systems, and processes held together by individual effort that don\'t survive growth. We work with operations teams to map, redesign, and automate their core workflows: procurement, project coordination, vendor management, logistics, and internal reporting. Your operations become predictable, scalable, and resilient — able to handle growth without fragility from processes dependent on constant human oversight.',
    ],
    'partnerships' => [
        'Partnerships & Business Development Automation',
        'Managing partner relationships, co-marketing activities, joint pipelines, and collaboration workflows manually is slow and creates missed opportunities at scale. We build automation systems for partnerships teams that track partner activities, automate communication workflows, handle onboarding for new partners, and keep all collaboration data organized and accessible. More bandwidth for relationship building, less coordination overhead that slows your partnership program down.',
    ],
    'product' => [
        'Product Team Automation',
        'Product teams sit at the intersection of engineering, design, data, and business — which means heavy coordination and information management overhead. We automate the operational layer of your product workflow: feedback collection and routing, roadmap status updates, release communication, bug triage, and cross-team reporting. Less time chasing updates across Slack and spreadsheets, more time on the product decisions that actually move the roadmap and deliver value to your users.',
    ],
    'procurement' => [
        'Procurement Process Automation',
        'Procurement involves high volumes of repetitive steps — vendor onboarding, purchase order creation, approval routing, invoice matching, and contract management — that are time-consuming and error-prone when handled manually. We build automation systems that standardize and accelerate the full procurement lifecycle. Faster approvals, cleaner vendor data, better compliance tracking, and full visibility into spend — without the manual overhead that makes procurement one of the most process-heavy departments to run.',
    ],
    'recruitment' => [
        'Recruitment Process Automation',
        'Recruiting teams spend enormous time on tasks that don\'t require human judgment — scheduling interviews, sending status updates, routing applications, collecting documents, and managing candidate communications. We automate the operational side of your recruitment process so your recruiters focus on evaluating candidates and building relationships. Faster time-to-hire, a consistent candidate experience at every touchpoint, and a process that scales without breaking when hiring volumes spike.',
    ],
    'sales' => [
        'Sales Department Automation',
        'Sales teams are most effective when they\'re selling — not updating CRM records, writing follow-up emails, or pulling pipeline reports. We automate the administrative and operational work across your sales department: lead assignment, pipeline updates, follow-up sequences, proposal generation, and performance reporting. Your reps get hours back each week. Your managers get real-time visibility. Your pipeline moves faster with fewer things falling through the cracks between stages.',
    ],

    // ── INDUSTRIES ─────────────────────────────────────────────────────────────
    'affiliate-marketing' => [
        'Automation for Affiliate Marketing',
        'Affiliate operations involve managing hundreds of partners, tracking performance across multiple networks, processing payouts, and running continuous outreach — all at high volume. Manual management doesn\'t scale here. We build automation systems for affiliate businesses that handle partner onboarding, campaign tracking, commission calculation, fraud detection workflows, and reporting. More partners managed efficiently, lower cost per operation, and the capacity to grow your program without growing your ops team proportionally.',
    ],
    'arbitration-companies' => [
        'Automation for Arbitration Companies',
        'Arbitration firms manage document-heavy, deadline-driven processes where accuracy and consistency are non-negotiable. We automate the operational workflows of arbitration practices — case intake, document management, scheduling, party communications, compliance tracking, and reporting. Custom systems that enforce process consistency across all cases, reduce administrative burden on your team, and ensure nothing falls through the cracks during proceedings where the stakes are too high for process failures.',
    ],
    'retail' => [
        'Automation for Ecommerce & Retail',
        'Ecommerce operations generate a constant stream of repetitive work — order processing, inventory updates, customer notifications, return handling, and performance reporting. We build automation systems that handle the operational backbone of your online store, connecting your platform, warehouse, payment systems, and customer communication into one seamless flow. More orders processed without more staff, faster fulfillment cycles, and a post-purchase experience that feels consistent and professional at any volume.',
    ],
    'fintech' => [
        'Automation for Fintech Companies',
        'Fintech businesses operate under strict compliance requirements while managing high transaction volumes and complex user workflows. We build automation systems that handle KYC and onboarding flows, transaction monitoring, compliance reporting, and operational processes — reliably and at scale. Whether you\'re a payments company, lending platform, or investment tool, we help you automate the operational layer so your team stays focused on product development and business growth.',
    ],
    'hr-recruiting' => [
        'Automation for HR & Recruiting Companies',
        'HR firms and recruiting agencies run high-volume, process-heavy operations — candidate sourcing, screening, interview scheduling, onboarding coordination, and client reporting. Every manual step adds up to hours that don\'t generate revenue. We build automation systems that handle the routine operational work in HR and recruitment, keeping your team focused on candidate relationships and client delivery rather than administrative tasks that can reliably run without human involvement.',
    ],
    'investment-management' => [
        'Automation for Investment Management',
        'Investment management firms deal with large data volumes, strict reporting requirements, and complex client workflows that are both time-sensitive and detail-critical. We build automation systems for investment teams that handle portfolio reporting, compliance documentation, client communications, and data aggregation. Accurate, timely, and compliant — automation designed to support your team\'s judgment and decision-making so analysts focus on analysis rather than report assembly and data preparation.',
    ],
    'lead-generation-agencies' => [
        'Automation for Lead Generation Agencies',
        'Lead generation is a volume business — and manual processes are the ceiling on how much volume you can handle profitably. We build automation systems for lead gen agencies: prospect sourcing, data enrichment, outreach sequences, lead qualification, client delivery, and performance reporting. Higher output with the same team, consistent quality across every campaign, and the operational capacity to take on more clients without proportional growth in headcount required to service them.',
    ],
    'legaltech' => [
        'Automation for LegalTech Companies',
        'LegalTech companies face the challenge of building scalable products in a domain with strict accuracy and compliance requirements. We help LegalTech businesses automate their internal workflows — document processing, client onboarding, product operations, and reporting — while strengthening the automation layer that makes your product more powerful. Whether you\'re an early-stage startup or a scaling platform, we deliver the operational efficiency that lets your team focus on product and growth.',
    ],
    'logistics' => [
        'Automation for Logistics Companies',
        'Logistics runs on tight margins and tight timelines — and manual processes create errors and delays that cost real money. We build automation systems for logistics companies that handle shipment tracking, documentation, route coordination, vendor communication, client notifications, and operational reporting. Fewer manual touchpoints across your supply chain, better accuracy at every stage, and real-time visibility into what\'s moving, where it is, and whether it will arrive on schedule.',
    ],
    'marketing-advertising' => [
        'Automation for Marketing Agencies',
        'Agency operations mean managing multiple clients, campaigns, and deliverables simultaneously — with a lot of repetitive coordination work filling the gaps. We build automation systems for marketing and advertising agencies that handle client reporting, campaign workflows, approval processes, data aggregation, and team coordination. Less time on operational overhead means more capacity for the creative and strategic work your clients actually pay for — and the ability to grow your client roster without growing your ops team at the same rate.',
    ],
    'real-estate' => [
        'Automation for Real Estate Companies',
        'Real estate involves high-touch client relationships supported by a lot of repetitive operational work — property listings, document collection, transaction coordination, lead follow-up, and CRM management. We build automation systems for real estate businesses that handle routine workflow so your agents focus on deals and relationships. Automated follow-up sequences, document management, transaction tracking, and performance reporting that keep every deal moving without constant manual chasing from your team.',
    ],
    'saas-startups' => [
        'Automation for SaaS Startups',
        'Early and growth-stage SaaS companies run lean — which means every hour of operational overhead is time not spent on product and customers. We help SaaS teams automate their business operations: customer onboarding flows, billing and subscription management, support workflows, internal reporting, and cross-tool integrations. Build the operational infrastructure that lets you scale revenue without scaling headcount at the same rate, and keep your team focused on building a great product.',
    ],
    'smes-general-growing-businesses' => [
        'Automation for Growing Businesses',
        'Growing businesses hit an operational ceiling when processes built for a small team start breaking under more volume, more customers, and more complexity. We help SMEs identify where automation creates the most immediate value and then build it. Whether it\'s your sales process, operations, reporting, or internal workflows — we design and implement automation that gives your team capacity to grow without the proportional increase in overhead that usually comes with it.',
    ],
    'wellness-mental-health' => [
        'Automation for Wellness & Mental Health Businesses',
        'Wellness and mental health businesses manage appointments, documentation, billing, and client communications with a high degree of care and consistency. Manual processes in this space create friction for both practitioners and clients. We build automation systems that handle scheduling, intake forms, follow-up communications, billing workflows, and internal reporting — so practitioners spend more time on client care and less time on administrative work that can be reliably handled without their direct involvement.',
    ],
];

// ── Run ──────────────────────────────────────────────────────────────────────
header('Content-Type: text/plain; charset=utf-8');
echo ($dry ? "=== DRY RUN ===\n\n" : "=== APPLYING ===\n\n");

$ok = $skip = $miss = 0;

foreach ($data as $slug => [$title, $desc]) {
    $page = row('SELECT id FROM solution_pages WHERE slug=?', [$slug]);
    if (!$page) {
        echo "❌  NOT FOUND slug=\"$slug\"\n";
        $miss++;
        continue;
    }
    $pid = $page['id'];

    // Update promo block content
    $block = row('SELECT id, content FROM sol_page_blocks WHERE page_id=? AND lang_id=? AND block_key="promo"', [$pid, $lang_id]);

    if ($block) {
        $content = json_decode($block['content'], true) ?: [];
        $content['title'] = $title;
        $content['text']  = $desc;
        if (!$dry) {
            update('sol_page_blocks', ['content' => json_encode($content, JSON_UNESCAPED_UNICODE)], ['id' => $block['id']]);
        }
        echo "✅  [update] $slug\n";
    } else {
        // Block doesn't exist yet — create it
        $content = json_encode([
            'title' => $title, 'text' => $desc,
            'btn1_text' => 'Try AI assistant', 'btn1_url' => '#',
            'btn2_text' => 'Free audit', 'btn2_url' => '#getintouch',
            'image_id' => null, 'image_url' => '',
        ], JSON_UNESCAPED_UNICODE);
        if (!$dry) {
            insert('sol_page_blocks', ['page_id'=>$pid,'lang_id'=>$lang_id,'block_key'=>'promo','label'=>'Hero / Promo','sort_order'=>0,'is_active'=>1,'content'=>$content]);
        }
        echo "✅  [insert] $slug\n";
    }
    $ok++;
}

echo "\n---\nUpdated: $ok | Not found: $miss\n";
