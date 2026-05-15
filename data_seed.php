<?php
/**
 * SIDIS Data Seed — inserts all sample content from original HTML files
 * Run ONCE: https://sidis.expandi.agency/data_seed.php
 * DELETE after use.
 */
require_once __DIR__ . '/includes/db.php';

$done = [];
$errors = [];

try {
    $pdo = db();

    // ── Language & Lang ID ───────────────────────────────────────────────────
    $lang = row('SELECT * FROM languages WHERE code=?', ['en']);
    if (!$lang) {
        die('Run setup.php first to create the English language.');
    }
    $lid = (int)$lang['id'];

    // ── Shared SVG icons ─────────────────────────────────────────────────────
    $svg_hub = '<svg width="44" height="44" viewbox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M40.1328 18.1328C38.4545 18.1328 37.037 19.2137 36.5031 20.7109H28.3149C28.1475 19.8895 27.8197 19.1273 27.3715 18.4513L29.4757 16.3471C30.9092 17.0258 32.6592 16.8086 33.8496 15.6202C35.0354 14.4343 35.2579 12.6831 34.577 11.2459L36.7681 9.0548C38.2029 9.73242 39.956 9.5138 41.1421 8.32769H41.1434C42.6491 6.81957 42.6491 4.36605 41.1421 2.85673C39.6328 1.34991 37.1793 1.35369 35.6725 2.85802C34.4874 4.0431 34.2683 5.79683 34.9455 7.2319L32.7446 9.43284C31.3124 8.78969 29.5206 9.01003 28.3813 10.1505C27.1915 11.3403 26.9745 13.0912 27.6529 14.5246L25.5489 16.6286C24.8729 16.1804 24.1106 15.8526 23.2892 15.6852V7.58287C24.7863 7.04894 25.8672 5.63148 25.8672 3.95312C25.8672 1.82067 24.1325 0 22 0C19.8675 0 18.1328 1.82067 18.1328 3.95312C18.1328 5.63148 19.2137 7.04894 20.7109 7.58287V15.6851C19.8895 15.8525 19.1273 16.1803 18.4513 16.6285L16.3471 14.5243C17.0258 13.0908 16.8086 11.3408 15.6202 10.1504C14.4797 9.00994 12.6885 8.78977 11.2559 9.43302L9.05463 7.23173C9.73191 5.79666 9.51268 4.04293 8.32769 2.85785C6.81837 1.34973 4.36605 1.35352 2.85673 2.85785C1.35111 4.36597 1.35111 6.81948 2.85802 8.32752C4.04336 9.51285 5.79666 9.73251 7.23207 9.05463L9.42313 11.2457C8.74259 12.6827 8.96362 14.4341 10.1506 15.6188C11.3404 16.8086 13.0913 17.0256 14.5246 16.3472L16.6286 18.4512C16.1805 19.1272 15.8527 19.8895 15.6853 20.7109H7.49693C6.963 19.2137 5.54555 18.1328 3.86719 18.1328C1.73473 18.1328 0 19.8675 0 22C0 24.1325 1.73473 25.8672 3.86719 25.8672C5.54555 25.8672 6.963 24.7863 7.49693 23.2891H15.6851C15.8525 24.1105 16.1803 24.8727 16.6285 25.5487L14.5142 27.6629C13.0831 27.0211 11.2924 27.2407 10.1504 28.3798C8.96457 29.5655 8.74208 31.3169 9.42296 32.7541L7.23181 34.9453C5.79691 34.268 4.04362 34.4872 2.85665 35.6722C1.35102 37.1803 1.35102 39.6339 2.85794 41.1432C4.36605 42.6488 6.81966 42.6486 8.3276 41.1432C9.51268 39.9568 9.73182 38.2031 9.05455 36.768L11.2456 34.577C12.6823 35.2573 14.4334 35.0361 15.6201 33.8493C16.8114 32.6557 17.0243 30.9056 16.3474 29.4751L18.4512 27.3713C19.1272 27.8194 19.8895 28.1472 20.7109 28.3146V36.4169C19.2136 36.9508 18.1327 38.3683 18.1327 40.0466C18.1327 42.1791 19.8675 43.9997 21.9999 43.9997C24.1324 43.9997 25.8671 42.1791 25.8671 40.0466C25.8671 38.3683 24.7862 36.9508 23.289 36.4169V28.3146C24.1104 28.1472 24.8726 27.8194 25.5486 27.3713L27.6526 29.4753C26.9744 30.9083 27.1908 32.6583 28.3797 33.8493C29.5656 35.035 31.3168 35.2577 32.754 34.5768L34.9452 36.7679C34.2679 38.203 34.4871 39.9571 35.6721 41.1431C37.1785 42.6469 39.6292 42.6506 41.1431 41.1418C42.6487 39.6337 42.6487 37.1802 41.1431 35.6721C39.956 34.4863 38.2022 34.2684 36.7679 34.9452L34.5769 32.7541C35.2572 31.3174 35.036 29.5664 33.8492 28.3797C32.706 27.2385 30.9158 27.0209 29.4854 27.6628L27.3712 25.5485C27.8193 24.8725 28.1471 24.1103 28.3145 23.2889H36.5027C37.0367 24.7861 38.4541 25.867 40.1325 25.867C42.2649 25.867 43.9997 24.1323 43.9997 21.9998C43.9997 19.8674 42.2653 18.1328 40.1328 18.1328Z" fill="currentColor"/></svg>';

    $svg_code = '<svg width="54" height="42" viewbox="0 0 54 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.6 33.2938C11.8773 33.2938 11.1987 33.0121 10.6902 32.5018L0.7911 22.6026C0.2817 22.095 0 21.4165 0 20.6938C0 19.9711 0.2817 19.2925 0.792 18.784L10.6911 8.88485C11.1987 8.37545 11.8773 8.09375 12.6 8.09375C14.0886 8.09375 15.3 9.30515 15.3 10.7938C15.3 11.5165 15.0183 12.195 14.508 12.7035L6.5178 20.6938L14.5089 28.6849C15.0192 29.1925 15.3 29.8711 15.3 30.5938C15.3 32.0824 14.0886 33.2938 12.6 33.2938Z" fill="currentColor"/><path d="M41.4031 34.2C39.9145 34.2 38.7031 32.9886 38.7031 31.5C38.7031 30.7773 38.9848 30.0987 39.4951 29.5902L47.4853 21.6L39.4942 13.6089C38.9848 13.0995 38.7031 12.4209 38.7031 11.7C38.7031 10.2114 39.9145 9 41.4031 9C42.1258 9 42.8044 9.2817 43.3129 9.792L53.212 19.6911C53.7223 20.2005 54.0031 20.8791 54.0031 21.6C54.0031 22.3227 53.7214 23.0013 53.2111 23.5098L43.312 33.4089C42.8044 33.9192 42.1258 34.2 41.4031 34.2Z" fill="currentColor"/><path d="M18.9031 41.4C17.4145 41.4 16.2031 40.1886 16.2031 38.7C16.2031 38.3418 16.2733 37.9917 16.4101 37.6605L32.611 1.6587C33.034 0.6507 34.0123 0 35.1031 0C36.5917 0 37.8031 1.2114 37.8031 2.7C37.8031 3.0582 37.7329 3.4083 37.5961 3.7395L21.3952 39.7413C20.9722 40.7493 19.9939 41.4 18.9031 41.4Z" fill="currentColor"/></svg>';

    $svg_branch = '<svg width="44" height="44" viewbox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M35.0582 5.27102C33.9884 4.20175 32.6898 3.66696 31.1618 3.66696C29.6345 3.66696 28.3359 4.20145 27.2663 5.27102C26.1971 6.3403 25.6625 7.63874 25.6625 9.16685C25.6625 10.1599 25.91 11.0813 26.4066 11.9311C26.9031 12.7809 27.5709 13.4447 28.4118 13.9219C28.4118 14.915 28.331 15.789 28.169 16.5432C28.0063 17.2974 27.7392 17.9607 27.3669 18.5341C26.9937 19.1071 26.6078 19.5937 26.2064 19.9949C25.805 20.3962 25.2416 20.773 24.516 21.1265C23.7905 21.4798 23.1168 21.7755 22.4963 22.0146C21.8759 22.2529 21.0401 22.5345 19.9898 22.8593C18.0798 23.4516 16.6096 23.9954 15.5782 24.4923V10.2549C16.4185 9.77779 17.087 9.11407 17.5834 8.26423C18.0798 7.41439 18.328 6.49281 18.328 5.49979C18.328 3.97218 17.7935 2.67344 16.7239 1.60416C15.6548 0.534989 14.3563 0 12.8283 0C11.3002 0 10.0018 0.534587 8.93219 1.60416C7.86301 2.67344 7.32812 3.97218 7.32812 5.49979C7.32812 6.49281 7.57635 7.41439 8.07271 8.26423C8.56917 9.11407 9.23751 9.77779 10.0779 10.2549V33.7451C9.23751 34.222 8.56917 34.8864 8.07271 35.736C7.57635 36.5861 7.32812 37.5077 7.32812 38.5004C7.32812 40.028 7.86261 41.3267 8.93219 42.3958C10.0018 43.465 11.3005 44 12.8283 44C14.356 44 15.6548 43.465 16.7239 42.3958C17.7932 41.3267 18.328 40.0279 18.328 38.5004C18.328 37.5077 18.0798 36.5861 17.5834 35.736C17.0868 34.8864 16.4185 34.222 15.5782 33.7451V33.0003C15.5782 31.6828 15.9744 30.7282 16.7669 30.1364C17.5593 29.5437 19.1778 28.8659 21.6226 28.1023C24.2011 27.2809 26.1398 26.507 27.4384 25.7811C31.716 23.3563 33.8741 19.403 33.9121 13.9218C34.753 13.4447 35.4208 12.7808 35.9173 11.931C36.4135 11.0811 36.6621 10.1598 36.6621 9.16675C36.6624 7.63904 36.1275 6.3406 35.0582 5.27102Z" fill="currentColor"/></svg>';

    $svg_workflow = '<svg width="44" height="44" viewbox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M40.1328 18.1328C38.4545 18.1328 37.037 19.2137 36.5031 20.7109H28.3149C28.1475 19.8895 27.8197 19.1273 27.3715 18.4513L29.4757 16.3471C30.9092 17.0258 32.6592 16.8086 33.8496 15.6202C35.0354 14.4343 35.2579 12.6831 34.577 11.2459L36.7681 9.0548C38.2029 9.73242 39.956 9.5138 41.1421 8.32769H41.1434C42.6491 6.81957 42.6491 4.36605 41.1421 2.85673C39.6328 1.34991 37.1793 1.35369 35.6725 2.85802C34.4874 4.0431 34.2683 5.79683 34.9455 7.2319L32.7446 9.43284C31.3124 8.78969 29.5206 9.01003 28.3813 10.1505C27.1915 11.3403 26.9745 13.0912 27.6529 14.5246L25.5489 16.6286C24.8729 16.1804 24.1106 15.8526 23.2892 15.6852V7.58287C24.7863 7.04894 25.8672 5.63148 25.8672 3.95312C25.8672 1.82067 24.1325 0 22 0C19.8675 0 18.1328 1.82067 18.1328 3.95312C18.1328 5.63148 19.2137 7.04894 20.7109 7.58287V15.6851C19.8895 15.8525 19.1273 16.1803 18.4513 16.6285L16.3471 14.5243C17.0258 13.0908 16.8086 11.3408 15.6202 10.1504C14.4797 9.00994 12.6885 8.78977 11.2559 9.43302L9.05463 7.23173C9.73191 5.79666 9.51268 4.04293 8.32769 2.85785C6.81837 1.34973 4.36605 1.35352 2.85673 2.85785C1.35111 4.36597 1.35111 6.81948 2.85802 8.32752C4.04336 9.51285 5.79666 9.73251 7.23207 9.05463L9.42313 11.2457C8.74259 12.6827 8.96362 14.4341 10.1506 15.6188C11.3404 16.8086 13.0913 17.0256 14.5246 16.3472L16.6286 18.4512C16.1805 19.1272 15.8527 19.8895 15.6853 20.7109H7.49693C6.963 19.2137 5.54555 18.1328 3.86719 18.1328C1.73473 18.1328 0 19.8675 0 22C0 24.1325 1.73473 25.8672 3.86719 25.8672C5.54555 25.8672 6.963 24.7863 7.49693 23.2891H15.6851C15.8525 24.1105 16.1803 24.8727 16.6285 25.5487L14.5142 27.6629C13.0831 27.0211 11.2924 27.2407 10.1504 28.3798C8.96457 29.5655 8.74208 31.3169 9.42296 32.7541L7.23181 34.9453C5.79691 34.268 4.04362 34.4872 2.85665 35.6722C1.35102 37.1803 1.35102 39.6339 2.85794 41.1432C4.36605 42.6488 6.81966 42.6486 8.3276 41.1432C9.51268 39.9568 9.73182 38.2031 9.05455 36.768L11.2456 34.577C12.6823 35.2573 14.4334 35.0361 15.6201 33.8493C16.8114 32.6557 17.0243 30.9056 16.3474 29.4751L18.4512 27.3713C19.1272 27.8194 19.8895 28.1472 20.7109 28.3146V36.4169C19.2136 36.9508 18.1327 38.3683 18.1327 40.0466C18.1327 42.1791 19.8675 43.9997 21.9999 43.9997C24.1324 43.9997 25.8671 42.1791 25.8671 40.0466C25.8671 38.3683 24.7862 36.9508 23.289 36.4169V28.3146C24.1104 28.1472 24.8726 27.8194 25.5486 27.3713L27.6526 29.4753C26.9744 30.9083 27.1908 32.6583 28.3797 33.8493C29.5656 35.035 31.3168 35.2577 32.754 34.5768L34.9452 36.7679C34.2679 38.203 34.4871 39.9571 35.6721 41.1431C37.1785 42.6469 39.6292 42.6506 41.1431 41.1418C42.6487 39.6337 42.6487 37.1802 41.1431 35.6721C39.956 34.4863 38.2022 34.2684 36.7679 34.9452L34.5769 32.7541C35.2572 31.3174 35.036 29.5664 33.8492 28.3797C32.706 27.2385 30.9158 27.0209 29.4854 27.6628L27.3712 25.5485C27.8193 24.8725 28.1471 24.1103 28.3145 23.2889H36.5027C37.0367 24.7861 38.4541 25.867 40.1325 25.867C42.2649 25.867 43.9997 24.1323 43.9997 21.9998C43.9997 19.8674 42.2653 18.1328 40.1328 18.1328Z" fill="currentColor"/></svg>';

    // ══════════════════════════════════════════════════════════════════════════
    // 1. HEADER NAVIGATION
    // ══════════════════════════════════════════════════════════════════════════
    // Always reseed nav (clear and rebuild)
    q('DELETE FROM nav_mega_subitems WHERE lang_id=?', [$lid]);
    q('DELETE FROM nav_mega_categories WHERE lang_id=?', [$lid]);
    q('DELETE FROM nav_items WHERE lang_id=?', [$lid]);
    if (true) {

        // ── Header top-level items
        $sol_id  = insert('nav_items', ['lang_id'=>$lid,'location'=>'header','title'=>'Solutions','url'=>'/solutions/','sort_order'=>0,'is_active'=>1,'has_mega'=>1,'mega_type'=>'solutions','mega_img_text'=>'']);
        $dept_id = insert('nav_items', ['lang_id'=>$lid,'location'=>'header','title'=>'Departments','url'=>'/departments/','sort_order'=>1,'is_active'=>1,'has_mega'=>1,'mega_type'=>'departments','mega_img_text'=>'Crafting innovative apps with a focus on user experience and scalability.']);
        $ind_id  = insert('nav_items', ['lang_id'=>$lid,'location'=>'header','title'=>'Industries','url'=>'/industries/','sort_order'=>2,'is_active'=>1,'has_mega'=>1,'mega_type'=>'solutions','mega_img_text'=>'']);
        insert('nav_items', ['lang_id'=>$lid,'location'=>'header','title'=>'Case Studies','url'=>'/cases/','sort_order'=>3,'is_active'=>1,'has_mega'=>0]);
        insert('nav_items', ['lang_id'=>$lid,'location'=>'header','title'=>'Blog','url'=>'/blog/','sort_order'=>4,'is_active'=>1,'has_mega'=>0]);
        insert('nav_items', ['lang_id'=>$lid,'location'=>'header','title'=>'Contact','url'=>'#getintouch','sort_order'=>5,'is_active'=>1,'has_mega'=>0]);

        // ── SOLUTIONS mega-menu
        $cats_solutions = [
            [0, 'Application development', 'End-to-end engineering services for seamless software delivery.', [
                ['SaaS platform development', 'Implement AI to transform your business operations, driving innovation and efficiency.'],
                ['Internal business systems development', 'Revolutionize customer service with AI-powered voice assistants, enhancing user experience.'],
                ['Custom CRM development', 'Automate manual tasks to reduce errors, improve accuracy, and free up valuable time.'],
                ['Custom ERP development', 'Deploy smart AI chatbots to handle customer queries, automate tasks, and boost satisfaction.'],
            ]],
            [1, 'AI & Automation', 'Crafting innovative apps with a focus on user experience and scalability.', [
                ['AI implementation for business', 'Implement AI to transform your business operations, driving innovation and efficiency.'],
                ['AI chatbots for business', 'Revolutionize customer service with AI-powered voice assistants, enhancing user experience.'],
                ['Voice AI assistants', 'Automate manual tasks to reduce errors, improve accuracy, and free up valuable time.'],
                ['Replacing manual work with automation', 'Deploy smart AI chatbots to handle customer queries, automate tasks, and boost satisfaction.'],
                ['Scaling business through automation', 'Use automation to scale your business, optimize processes, and achieve unprecedented growth.'],
            ]],
            [2, 'Business process automation', 'Streamlining processes for maximum efficiency and productivity.', [
                ['Business process automation', 'Automate and optimize your core business workflows end-to-end.'],
                ['Sales automation', 'Automate your sales pipeline to close more deals in less time.'],
                ['Marketing automation', 'Scale your marketing with smart automation tools and workflows.'],
                ['Customer support automation', 'Deliver faster, smarter customer support with automation.'],
                ['Operations automation', 'Optimize operations by automating repetitive operational tasks.'],
                ['Unified business management systems', 'Connect all your business tools into one unified platform.'],
            ]],
            [3, 'Integrations & Data', 'Connecting systems and data for seamless operations and informed strategies.', [
                ['Systems & API integrations', 'Connect your existing tools with seamless API integrations.'],
                ['Data & reporting automation', 'Automate your data pipelines and reporting for real-time insights.'],
            ]],
            [4, 'Advisory & Optimization', 'Expert guidance to transform and optimize your business processes.', [
                ['Business process analysis & optimization', 'Analyze and optimize your workflows with expert guidance.'],
            ]],
        ];

        foreach ($cats_solutions as [$sort, $title, $desc, $subs]) {
            $cat_id = insert('nav_mega_categories', ['nav_item_id'=>$sol_id,'lang_id'=>$lid,'title'=>$title,'description'=>$desc,'sort_order'=>$sort]);
            foreach ($subs as $si => $sub) {
                insert('nav_mega_subitems', ['category_id'=>$cat_id,'lang_id'=>$lid,'title'=>$sub[0],'description'=>$sub[1],'url'=>'#','icon_svg'=>$svg_hub,'sort_order'=>$si]);
            }
        }

        // ── DEPARTMENTS mega-menu (flat grid, 15 items → 3 columns of 5)
        $svg_hub40 = '<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M36.4844 16.4844C34.9586 16.4844 33.67 17.467 33.1846 18.8281H25.7408C25.5886 18.0814 25.2906 17.3884 24.8832 16.7739L26.7961 14.861C28.0993 15.478 29.6902 15.2805 30.7723 14.2002C31.8504 13.1221 32.0527 11.5301 31.4337 10.2235L33.4255 8.23164C34.7299 8.84766 36.3236 8.64891 37.402 7.57062H37.4031C38.7719 6.19961 38.7719 3.96914 37.402 2.59703C36.0298 1.22719 33.7994 1.23063 32.4295 2.5982C31.3522 3.67555 31.153 5.26984 31.7687 6.57445L29.7678 8.57531C28.4658 7.99062 26.837 8.19094 25.8012 9.22773C24.7195 10.3094 24.5223 11.9011 25.139 13.2041L23.2262 15.1169C22.6117 14.7095 21.9188 14.4115 21.172 14.2593V6.89352C22.533 6.40812 23.5156 5.11953 23.5156 3.59375C23.5156 1.65516 21.9386 0 20 0C18.0614 0 16.4844 1.65516 16.4844 3.59375C16.4844 5.11953 17.467 6.40812 18.8281 6.89352V14.2592C18.0814 14.4114 17.3884 14.7094 16.7739 15.1168L14.861 13.2039C15.478 11.9007 15.2805 10.3098 14.2002 9.22766C13.1634 8.19086 11.535 7.9907 10.2327 8.57547L8.23148 6.5743C8.84719 5.26969 8.64789 3.67539 7.57062 2.59805C6.19852 1.22703 3.96914 1.23047 2.59703 2.59805C1.22828 3.96906 1.22828 6.19953 2.5982 7.57047C3.67578 8.64805 5.26969 8.84773 6.57461 8.23148L8.56648 10.2234C7.94781 11.5297 8.14875 13.1219 9.22781 14.1989C10.3095 15.2805 11.9012 15.4778 13.2042 14.8611L15.117 16.7738C14.7095 17.3884 14.4116 18.0813 14.2594 18.828H6.81539C6.33 17.467 5.04141 16.4844 3.51562 16.4844C1.57703 16.4844 0 18.0614 0 20C0 21.9386 1.57703 23.5156 3.51562 23.5156C5.04141 23.5156 6.33 22.533 6.81539 21.1719H14.2592C14.4114 21.9186 14.7094 22.6116 15.1168 23.2261L13.1948 25.1481C11.8938 24.5646 10.2658 24.7643 9.22766 25.7998C8.14961 26.8777 7.94734 28.4699 8.56633 29.7765L6.57438 31.7684C5.26992 31.1527 3.67602 31.352 2.59695 32.4293C1.2282 33.8003 1.2282 36.0308 2.59812 37.4029C3.96914 38.7716 6.19969 38.7715 7.57055 37.4029C8.64789 36.3244 8.84711 34.7301 8.23141 33.4255L10.2233 31.4336C11.5294 32.0521 13.1213 31.851 14.2001 30.7721C15.2831 29.687 15.4766 28.096 14.8612 26.7955L16.7738 24.883C17.3884 25.2904 18.0813 25.5884 18.828 25.7405V33.1063C17.467 33.5916 16.4843 34.8802 16.4843 36.406C16.4843 38.3446 18.0613 39.9998 19.9999 39.9998C21.9385 39.9998 23.5155 38.3446 23.5155 36.406C23.5155 34.8802 22.5329 33.5916 21.1718 33.1063V25.7405C21.9185 25.5884 22.6115 25.2904 23.226 24.883L25.1388 26.7957C24.5222 28.0984 24.7189 29.6894 25.7998 30.7721C26.8778 31.85 28.4698 32.0524 29.7764 31.4334L31.7684 33.4254C31.1527 34.73 31.352 36.3246 32.4292 37.4028C33.7987 38.7699 36.0266 38.7733 37.4028 37.4016C38.7716 36.0306 38.7716 33.8002 37.4028 32.4292C36.3236 31.3512 34.7293 31.153 33.4254 31.7684L31.4335 29.7765C32.052 28.4704 31.8509 26.8785 30.772 25.7997C29.7327 24.7623 28.1052 24.5645 26.8049 25.148L24.8829 23.2259C25.2903 22.6114 25.5883 21.9184 25.7405 21.1717H33.1843C33.6697 22.5328 34.9583 23.5155 36.4841 23.5155C38.4227 23.5155 39.9997 21.9384 39.9997 19.9998C39.9997 18.0613 38.423 16.4844 36.4844 16.4844Z" fill="currentColor"/></svg>';
        $svg_code40 = '<svg width="40" height="31" viewbox="0 0 40 31" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.33333 24.6676C8.798 24.6676 8.29533 24.459 7.91867 24.081L0.586 16.7483C0.208667 16.3723 0 15.8696 0 15.3343C0 14.799 0.208667 14.2963 0.586667 13.9196L7.91933 6.58698C8.29533 6.20964 8.798 6.00098 9.33333 6.00098C10.436 6.00098 11.3333 6.89831 11.3333 8.00098C11.3333 8.53631 11.1247 9.03898 10.7467 9.41564L4.828 15.3343L10.7473 21.2536C11.1253 21.6296 11.3333 22.1323 11.3333 22.6676C11.3333 23.7703 10.436 24.6676 9.33333 24.6676Z" fill="currentColor"/><path d="M30.667 25.3317C29.5643 25.3317 28.667 24.4344 28.667 23.3317C28.667 22.7964 28.8757 22.2937 29.2537 21.917L35.1723 15.9984L29.253 10.079C28.8757 9.70171 28.667 9.19904 28.667 8.66504C28.667 7.56237 29.5643 6.66504 30.667 6.66504C31.2023 6.66504 31.705 6.87371 32.0817 7.25171L39.4143 14.5844C39.7923 14.9617 40.0003 15.4644 40.0003 15.9984C40.0003 16.5337 39.7917 17.0364 39.4137 17.413L32.081 24.7457C31.705 25.1237 31.2023 25.3317 30.667 25.3317Z" fill="currentColor"/><path d="M14.002 30.6667C12.8993 30.6667 12.002 29.7693 12.002 28.6667C12.002 28.4013 12.054 28.142 12.1553 27.8967L24.156 1.22867C24.4693 0.482 25.194 0 26.002 0C27.1046 0 28.002 0.897333 28.002 2C28.002 2.26533 27.95 2.52467 27.8486 2.77L15.848 29.438C15.5346 30.1847 14.81 30.6667 14.002 30.6667Z" fill="currentColor"/></svg>';
        $svg_branch40 = '<svg width="40" height="40" viewbox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M31.8752 4.79184C30.9026 3.81977 29.7221 3.3336 28.333 3.3336C26.9445 3.3336 25.764 3.8195 24.7916 4.79184C23.8196 5.76391 23.3336 6.94431 23.3336 8.3335C23.3336 9.23624 23.5587 10.074 24.0101 10.8464C24.4614 11.619 25.0685 12.2225 25.833 12.6563C25.833 13.5591 25.7596 14.3536 25.6123 15.0393C25.4643 15.7249 25.2215 16.328 24.8831 16.8491C24.5438 17.3701 24.193 17.8125 23.8281 18.1772C23.4632 18.542 22.951 18.8846 22.2913 19.2059C21.6318 19.5271 21.0194 19.7959 20.4553 20.0133C19.8912 20.2299 19.1314 20.4859 18.1766 20.7812C16.4402 21.3196 15.1037 21.814 14.1661 22.2657V9.32262C14.93 8.8889 15.5377 8.28552 15.989 7.51294C16.4402 6.74035 16.6659 5.90255 16.6659 4.99981C16.6659 3.61107 16.18 2.4304 15.2077 1.45833C14.2357 0.486353 13.0553 0 11.6662 0C10.277 0 9.0966 0.485988 8.12425 1.45833C7.15228 2.4304 6.66602 3.61107 6.66602 4.99981C6.66602 5.90255 6.89168 6.74035 7.34291 7.51294C7.79424 8.28552 8.40182 8.8889 9.16583 9.32262V30.6774C8.40182 31.1109 7.79424 31.7149 7.34291 32.4872C6.89168 33.2601 6.66602 34.0979 6.66602 35.0004C6.66602 36.3891 7.15191 37.5697 8.12425 38.5417C9.0966 39.5136 10.2773 40 11.6662 40C13.055 40 14.2357 39.5136 15.2077 38.5417C16.1797 37.5697 16.6659 36.389 16.6659 35.0004C16.6659 34.0979 16.4402 33.2601 15.989 32.4872C15.5376 31.7149 14.93 31.1109 14.1661 30.6774V30.0003C14.1661 28.8026 14.5263 27.9348 15.2467 27.3967C15.9671 26.8579 17.4385 26.2417 19.661 25.5475C22.0051 24.8008 23.7676 24.0973 24.948 23.4374C28.8368 21.233 30.7987 17.6391 30.8333 12.6562C31.5977 12.2225 32.2049 11.6189 32.6562 10.8464C33.1072 10.0738 33.3333 9.23615 33.3333 8.33341C33.3335 6.94458 32.8473 5.76418 31.8752 4.79184Z" fill="currentColor"/></svg>';

        // 15 departments, each with icon — stored as flat list under one category
        $dept_items = [
            ['Operations',$svg_hub40],['Sales',$svg_hub40],['Marketing',$svg_code40],
            ['Finance',$svg_hub40],['HR',$svg_hub40],
            ['Recruitment',$svg_hub40],['Product',$svg_hub40],['IT / Engineering',$svg_code40],
            ['Analytics / BI',$svg_branch40],['Executive / Management',$svg_hub40],
            ['Legal',$svg_hub40],['Compliance',$svg_hub40],['Partnerships',$svg_hub40],
            ['Procurement',$svg_hub40],['Customer Support',$svg_hub40],
        ];
        $dept_cat_id = insert('nav_mega_categories', ['nav_item_id'=>$dept_id,'lang_id'=>$lid,'title'=>'All Departments','description'=>'','sort_order'=>0]);
        foreach ($dept_items as $si => $di) {
            insert('nav_mega_subitems', ['category_id'=>$dept_cat_id,'lang_id'=>$lid,'title'=>$di[0],'description'=>'','url'=>'#','icon_svg'=>$di[1],'sort_order'=>$si]);
        }

        // ── INDUSTRIES mega-menu
        $cats_inds = [
            [0, 'Tech & Digital', 'Solutions for technology companies and digital businesses.', [
                ['SaaS Startups','Scale your SaaS product with powerful automation workflows.'],
                ['LegalTech / Fintech','Automate compliance, document processing, and financial workflows.'],
                ['Arbitration Companies','Streamline case management and document automation.'],
            ]],
            [1, 'Marketing & Growth', 'Industries driven by marketing and performance.', [
                ['Affiliate Marketing','Automate tracking, reconciliation, and partner management.'],
                ['Lead Generation Agencies','Automate outreach, qualification, and reporting pipelines.'],
                ['Marketing & Advertising','Scale campaigns with smart marketing automation.'],
                ['Agencies','Automate client reporting, onboarding, and delivery workflows.'],
            ]],
            [2, 'Finance & Professional Services', 'Automation for financial and professional industries.', [
                ['Fintech','Automate financial operations, compliance, and reporting.'],
                ['Investment Management','Streamline portfolio tracking and investor communication.'],
                ['HR & Recruiting','Automate candidate sourcing, screening, and onboarding.'],
            ]],
            [3, 'Commerce & Logistics', 'Physical and digital commerce automation.', [
                ['Ecommerce','Automate orders, inventory, and customer journeys.'],
                ['Retail','Connect your retail stack with smart automation.'],
                ['Logistics','Automate shipment tracking, routing, and documentation.'],
                ['Real Estate','Automate property management, leads, and contracts.'],
            ]],
            [4, 'Health & Wellness', 'Automation for health and wellness businesses.', [
                ['Wellness & Mental Health','Automate scheduling, client follow-up, and billing.'],
            ]],
        ];

        foreach ($cats_inds as [$sort, $title, $desc, $subs]) {
            $cat_id = insert('nav_mega_categories', ['nav_item_id'=>$ind_id,'lang_id'=>$lid,'title'=>$title,'description'=>$desc,'sort_order'=>$sort]);
            foreach ($subs as $si => $sub) {
                insert('nav_mega_subitems', ['category_id'=>$cat_id,'lang_id'=>$lid,'title'=>$sub[0],'description'=>$sub[1],'url'=>'#','icon_svg'=>$svg_hub,'sort_order'=>$si]);
            }
        }

        $done[] = 'Header navigation seeded (Solutions, Departments, Industries mega-menu + nav items)';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // 2. FOOTER NAVIGATION
    // ══════════════════════════════════════════════════════════════════════════
    // Footer already cleared above, just seed it
    if (true) {

        $footer_cols = [
            ['Explore Solutions', [
                'Business process automation','AI implementation for business','Custom CRM development',
                'Custom ERP development','Internal business systems development','SaaS platform development',
                'Systems & API integrations','Sales automation','Marketing automation',
                'Customer support automation','Operations automation','Unified business management systems',
                'AI chatbots for business','Voice AI assistants','Data & reporting automation',
                'Replacing manual work with automation','Scaling business through automation',
                'Business process analysis & optimization',
            ]],
            ['Departments', [
                'Operations','Sales','Marketing','Customer Support','Finance','HR','Recruitment',
                'Product','IT / Engineering','Analytics / BI','Executive / Management','Legal',
                'Compliance','Partnerships',
            ]],
            ['Industries', [
                'Affiliate Marketing','HR & Recruiting','Fintech','Real Estate','Retail','Ecommerce',
                'Investment Management','Logistics','Wellness & Mental Health','Lead Generation Agencies',
                'Marketing & Advertising','Agencies','LegalTech','Arbitration Companies',
                'SaaS Startups','SMEs (general growing businesses)',
            ]],
            ['Company', [
                'Solutions','Departments','Industries','Case studies','Blog','Terms & Conditions',
            ]],
        ];

        foreach ($footer_cols as $ci => [$col_title, $links]) {
            $parent_id = insert('nav_items', ['lang_id'=>$lid,'location'=>'footer','title'=>$col_title,'url'=>'#','sort_order'=>$ci,'is_active'=>1,'has_mega'=>0]);
            foreach ($links as $li => $link) {
                $url = '#';
                if ($link === 'Case studies') $url = '/cases/';
                if ($link === 'Blog') $url = '/blog/';
                if ($link === 'Solutions') $url = '/solutions/';
                if ($link === 'Departments') $url = '/departments/';
                if ($link === 'Industries') $url = '/industries/';
                insert('nav_items', ['lang_id'=>$lid,'location'=>'footer','title'=>$link,'url'=>$url,'sort_order'=>$li,'is_active'=>1,'has_mega'=>0,'parent_id'=>$parent_id]);
            }
        }
        $done[] = 'Footer navigation seeded (4 columns with all links)';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // 3. WHY SLIDES
    // ══════════════════════════════════════════════════════════════════════════
    $existing_why = row('SELECT COUNT(*) as c FROM home_why_slides WHERE lang_id=?', [$lid])['c'];
    if ((int)$existing_why == 0) {
        $why_slides = [
            [0, $svg_code, "Manual Processes Kill\nProductivity", 'RPA automates repetitive tasks instantly, saving 30–50% of time spent on admin.'],
            [1, $svg_hub, "Outdated Tech Slows\nYou Down", 'Disconnected tools and legacy software create bottlenecks that kill growth.'],
            [2, $svg_branch, "Messy Data &\nSlow Decisions", 'Poor data management leads to bad decisions, missed opportunities, and revenue loss.'],
            [3, $svg_code, "High Error Rates\nCost You Money", 'Manual data entry causes costly mistakes. Automation eliminates human error at scale.'],
            [4, $svg_hub, "Scaling Without\nScaling Costs", 'Grow your output without proportionally growing your team using smart automation.'],
        ];
        foreach ($why_slides as [$sort, $icon, $title, $text]) {
            insert('home_why_slides', ['lang_id'=>$lid,'sort_order'=>$sort,'icon_svg'=>$icon,'title'=>$title,'text'=>$text]);
        }
        $done[] = 'Why slides seeded (5 slides with icons)';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // 4. TAXONOMY TERMS
    // ══════════════════════════════════════════════════════════════════════════
    $existing_terms = row('SELECT COUNT(*) as c FROM terms')['c'];
    if ((int)$existing_terms == 0) {
        $all_terms = [
            'solution' => [
                'Workflow Automation','Custom Software Development','AI Implementation',
                'SaaS Platform Development','Custom CRM Development','Custom ERP Development',
                'Internal Business Systems','Systems & API Integrations','Sales Automation',
                'Marketing Automation','Customer Support Automation','Operations Automation',
                'AI Chatbots','Voice AI Assistants','Data & Reporting Automation',
                'Business Process Optimization','Bespoke Software Solutions',
            ],
            'department' => [
                'Operations','Sales','Marketing','Customer Support','Finance','HR',
                'Recruitment','Product','IT / Engineering','Analytics / BI',
                'Executive / Management','Legal','Compliance','Partnerships',
            ],
            'industry' => [
                'Affiliate Marketing','HR & Recruiting','Fintech','Real Estate','Retail',
                'Ecommerce','Investment Management','Logistics','Wellness & Mental Health',
                'Lead Generation Agencies','Marketing & Advertising','Agencies','LegalTech',
                'Arbitration Companies','SaaS Startups','SMEs',
            ],
        ];
        foreach ($all_terms as $type => $terms) {
            foreach ($terms as $i => $name) {
                $slug_val = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
                $tid = insert('terms', ['type'=>$type,'slug'=>$slug_val,'sort_order'=>$i,'is_active'=>1]);
                insert('terms_t', ['term_id'=>$tid,'lang_id'=>$lid,'name'=>$name]);
            }
        }
        $done[] = 'Taxonomy terms seeded (solutions, departments, industries)';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // 5. SOLUTION ITEMS (homepage Solutions tabs)
    // ══════════════════════════════════════════════════════════════════════════
    $existing_sol = row('SELECT COUNT(*) as c FROM solution_items')['c'];
    if ((int)$existing_sol == 0) {
        $sol_desc = 'We help SMEs automate repetitive tasks and optimise everyday processes. From mapping workflows to integrating automation tools with your existing systems, we save time, reduce errors, and improve operational efficiency.';

        $svg_soft = '<svg width="64" height="64" viewbox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.9333 46.937C14.0768 46.937 13.2725 46.6031 12.6699 45.9983L0.9376 34.266C0.333867 33.6644 0 32.8602 0 32.0036C0 31.1471 0.333867 30.3428 0.938667 29.7402L12.6709 18.0079C13.2725 17.4042 14.0768 17.0703 14.9333 17.0703C16.6976 17.0703 18.1333 18.506 18.1333 20.2703C18.1333 21.1268 17.7995 21.9311 17.1947 22.5338L7.7248 32.0036L17.1957 41.4746C17.8005 42.0762 18.1333 42.8804 18.1333 43.737C18.1333 45.5012 16.6976 46.937 14.9333 46.937Z" fill="currentColor"/><path d="M49.0672 47.9995C47.3029 47.9995 45.8672 46.5637 45.8672 44.7995C45.8672 43.9429 46.2011 43.1387 46.8059 42.536L56.2757 33.0661L46.8048 23.5952C46.2011 22.9915 45.8672 22.1872 45.8672 21.3328C45.8672 19.5685 47.3029 18.1328 49.0672 18.1328C49.9237 18.1328 50.728 18.4667 51.3307 19.0715L63.0629 30.8037C63.6677 31.4075 64.0005 32.2117 64.0005 33.0661C64.0005 33.9227 63.6667 34.7269 63.0619 35.3296L51.3296 47.0619C50.728 47.6667 49.9237 47.9995 49.0672 47.9995Z" fill="currentColor"/><path d="M22.4031 56.5354C20.6389 56.5354 19.2031 55.0997 19.2031 53.3354C19.2031 52.9109 19.2863 52.496 19.4485 52.1034L38.6495 9.43462C39.1509 8.23995 40.3103 7.46875 41.6031 7.46875C43.3674 7.46875 44.8031 8.90448 44.8031 10.6688C44.8031 11.0933 44.7199 11.5082 44.5578 11.9008L25.3567 54.5696C24.8554 55.7642 23.6959 56.5354 22.4031 56.5354Z" fill="currentColor"/></svg>';

        $svg_bespoke = '<svg width="64" height="64" viewbox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M50.9988 7.66694C49.4426 6.11163 47.5538 5.33376 45.3312 5.33376C43.1097 5.33376 41.2208 6.11119 39.665 7.66694C38.1099 9.22225 37.3323 11.1109 37.3323 13.3336C37.3323 14.778 37.6923 16.1183 38.4146 17.3543C39.1367 18.5904 40.1081 19.556 41.3312 20.2501C41.3312 21.6945 41.2138 22.9658 40.9781 24.0628C40.7413 25.1599 40.3528 26.1247 39.8114 26.9586C39.2685 27.7921 38.7072 28.4999 38.1234 29.0836C37.5395 29.6672 36.72 30.2153 35.6646 30.7295C34.6093 31.2434 33.6294 31.6735 32.7269 32.0212C31.8244 32.3679 30.6087 32.7775 29.081 33.2499C26.3028 34.1114 24.1643 34.9024 22.6642 35.6251V14.9162C23.8864 14.2222 24.8587 13.2568 25.5808 12.0207C26.3028 10.7846 26.6639 9.44408 26.6639 7.99969C26.6639 5.77772 25.8864 3.88864 24.3307 2.33333C22.7755 0.778165 20.8869 0 18.6643 0C16.4416 0 14.553 0.777581 12.9972 2.33333C11.4421 3.88864 10.6641 5.77772 10.6641 7.99969C10.6641 9.44408 11.0251 10.7846 11.7471 12.0207C12.4692 13.2568 13.4413 14.2222 14.6638 14.9162V49.0838C13.4413 49.7775 12.4692 50.7439 11.7471 51.9796C11.0251 53.2162 10.6641 54.5567 10.6641 56.0006C10.6641 58.2226 11.4415 60.1115 12.9972 61.6667C14.553 63.2218 16.4421 64 18.6643 64C20.8865 64 22.7755 63.2218 24.3307 61.6667C25.886 60.1115 26.6639 58.2224 26.6639 56.0006C26.6639 54.5567 26.3028 53.2162 25.5808 51.9796C24.8586 50.7439 23.8864 49.7775 22.6642 49.0838V48.0005C22.6642 46.0841 23.2405 44.6956 24.3932 43.8347C25.5458 42.9726 27.9 41.9868 31.456 40.876C35.2066 39.6813 38.0265 38.5557 39.9153 37.4998C46.1373 33.9728 49.2764 28.2225 49.3317 20.25C50.5548 19.556 51.5262 18.5903 52.2483 17.3542C52.97 16.118 53.3317 14.7778 53.3317 13.3334C53.3321 11.1113 52.5541 9.22269 50.9988 7.66694Z" fill="currentColor"/></svg>';

        $solutions = [
            ['solution', 0, $svg_workflow, 'Workflow Automations', $sol_desc, 'Learn more', '#'],
            ['solution', 1, $svg_soft,     'Custom Software Development', $sol_desc, 'Learn more', '#'],
            ['solution', 2, $svg_bespoke,  'Bespoke Software Solutions', $sol_desc, 'Learn more', '#'],
            ['department', 0, $svg_workflow,'Operations Automation', 'We automate operational workflows so your team focuses on high-value work.', 'Learn more', '#'],
            ['department', 1, $svg_hub,    'Sales Automation', 'Automate your sales pipeline to close more deals with less manual effort.', 'Learn more', '#'],
            ['department', 2, $svg_soft,   'Marketing Automation', 'Scale your marketing efforts with intelligent automation tools.', 'Learn more', '#'],
            ['industry', 0, $svg_bespoke,  'SaaS Startups', 'Purpose-built automation solutions to help SaaS companies scale efficiently.', 'Learn more', '#'],
            ['industry', 1, $svg_workflow, 'Ecommerce', 'Automate orders, inventory, and customer communication at scale.', 'Learn more', '#'],
            ['industry', 2, $svg_hub,      'Affiliate Marketing', 'Automate partner tracking, payouts, and reporting pipelines.', 'Learn more', '#'],
        ];

        foreach ($solutions as [$type, $sort, $icon, $title, $desc, $btn_text, $btn_url]) {
            $iid = insert('solution_items', ['type'=>$type,'icon_svg'=>$icon,'sort_order'=>$sort,'is_active'=>1,'page_slug'=>null]);
            insert('solution_items_t', ['item_id'=>$iid,'lang_id'=>$lid,'title'=>$title,'description'=>$desc,'btn_text'=>$btn_text,'btn_url'=>$btn_url]);
        }
        $done[] = 'Solution items seeded (3 solutions, 3 departments, 3 industries)';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // 6. CASE STUDIES (4 projects from homepage)
    // ══════════════════════════════════════════════════════════════════════════
    $existing_cases = row('SELECT COUNT(*) as c FROM cases')['c'];
    if ((int)$existing_cases == 0) {
        $text = 'RPA automates repetitive tasks instantly, saving 30–50% of time spent on admin. We analyzed existing workflows, identified bottlenecks, and deployed a tailored automation solution that transformed operations.';

        $cases_data = [
            ['apex-agency-invoice', 'Apex Agency', 'Automated Invoice Processing',
             'We automated the entire invoice processing workflow, reducing manual data entry by 95% and cutting processing time from 4 hours to under 10 minutes.',
             'Saved 30 hours/week · Reduced errors by 95% · ROI in 6 weeks',
             './assets/img/projects/image-1.webp', null,
             ['Customer Support','Affiliate Marketing'], ['Operations'],
            ],
            ['nda-data-insights', 'NDA (Confidential)', 'Streamlined Data, Faster Insights',
             'Connected multiple data sources into a unified dashboard, enabling real-time reporting and eliminating manual data exports.',
             'Saved 11 hours/week · Real-time dashboards · 3× faster reporting',
             './assets/img/projects/image-2.webp', null,
             ['HR & Recruiting','Construction'], ['HR'],
            ],
            ['cloud-origin-extraction', 'Cloud Origin', 'Smart Data Extraction',
             'Built a custom data extraction system that pulls structured data from unstructured documents automatically, with 99.2% accuracy.',
             'Saved 44 hours/week · 99.2% accuracy · Zero manual extraction',
             './assets/img/projects/image-3.webp', null,
             ['Ecommerce','Logistics'], ['Operations'],
            ],
            ['innovate-healthcare', 'Innovate Solutions', 'Healthcare Invoice Automation',
             'Automated the medical billing workflow, integrating with the existing EHR system to generate, validate, and submit invoices automatically.',
             'Saved 17 hours/week · 100% compliance · Faster reimbursements',
             './assets/img/projects/image-4.webp', null,
             ['Wellness & Mental Health'], ['Finance'],
            ],
        ];

        foreach ($cases_data as [$slug, $company, $title, $desc, $key_result, $img_path, $logo_path, $industries, $depts]) {
            $case_id = insert('cases', [
                'slug' => $slug, 'is_active' => 1, 'is_featured' => 1,
                'sort_order' => 0, 'company_logo_id' => null, 'featured_image_id' => null,
            ]);
            insert('cases_t', [
                'case_id' => $case_id, 'lang_id' => $lid,
                'title' => $title, 'description' => $desc,
                'overview_text' => $text,
                'location' => 'Europe', 'cooperation_period' => '3 months',
                'meta_title' => $title . ' — SIDIS', 'meta_description' => $desc,
            ]);
            insert('case_key_results', ['case_id'=>$case_id,'lang_id'=>$lid,'text'=>$key_result,'sort_order'=>0]);

            // Tags: store image path as a note (images are static assets)
            // Store the image path in case_services as reference for now
            insert('case_services', ['case_id'=>$case_id,'lang_id'=>$lid,'service_name'=>'Workflow Automation','sort_order'=>0]);
            insert('case_services', ['case_id'=>$case_id,'lang_id'=>$lid,'service_name'=>'Process Automation','sort_order'=>1]);

            // Link terms
            foreach (array_merge($industries, $depts) as $term_name) {
                $term = row('SELECT t.id,t.type FROM terms t JOIN terms_t tt ON t.id=tt.term_id WHERE tt.name=? AND tt.lang_id=?', [$term_name, $lid]);
                if ($term) {
                    insert('case_terms', ['case_id'=>$case_id,'term_id'=>$term['id'],'type'=>$term['type']]);
                }
            }
        }
        $done[] = 'Case studies seeded (4 projects with terms)';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // 7. REVIEWS
    // ══════════════════════════════════════════════════════════════════════════
    $existing_reviews = row('SELECT COUNT(*) as c FROM reviews')['c'];
    if ((int)$existing_reviews == 0) {
        $reviews_data = [
            [0, 'RPA automates repetitive tasks instantly, saving 30–50% of time spent on admin.',
             "Since implementing RPA, 'StellarTech Solutions' has transformed our operational landscape. We've not only optimized our workflows but also unlocked new levels of efficiency and accuracy. RPA truly drives innovation.\n\nThe results exceeded our expectations — the team at SIDIS understood our business deeply and delivered a solution that just works.",
             'Michael Turner', 'CEO, Solaris Dynamics', 'https://linkedin.com'],
            [1, 'The automation solution paid for itself within the first month.',
             "Working with SIDIS was a game-changer for our business. They mapped our entire sales workflow and identified automation opportunities we hadn't even considered.\n\nWe went from 15 hours of manual admin per week to under 2 hours. The quality and attention to detail were outstanding.",
             'Evelyn Hayes', 'CTO, Quantum Leap Technologies', 'https://linkedin.com'],
            [2, 'Best investment we made this year — hands down.',
             "SIDIS delivered exactly what they promised, on time and within budget. Our data extraction process went from a 3-person daily task to a fully automated pipeline that runs every night.\n\nThe ROI was clear within weeks. I recommend them to any business looking to automate seriously.",
             'David Chen', 'COO, CloudStream Analytics', 'https://linkedin.com'],
            [3, "Finally, an automation partner that speaks our language.",
             "We'd tried other automation tools before but always hit walls. SIDIS took the time to understand our unique processes and built something truly custom.\n\nSix months later, our team handles 3× the volume with the same headcount. The support after delivery has also been excellent.",
             'Sarah Mitchell', 'Head of Operations, NexaFlow', 'https://linkedin.com'],
        ];

        foreach ($reviews_data as [$sort, $quote, $text, $name, $title_pos, $linkedin]) {
            $rid = insert('reviews', ['sort_order'=>$sort,'author_image_id'=>null,'linkedin_url'=>$linkedin,'rating_image_id'=>null,'is_active'=>1]);
            insert('reviews_t', ['review_id'=>$rid,'lang_id'=>$lid,'quote'=>$quote,'text'=>$text,'author_name'=>$name,'author_title'=>$title_pos]);
        }
        $done[] = 'Reviews seeded (4 client testimonials)';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // 7b. ADD MISSING COLUMNS & TABLES (migration for existing DBs)
    // ══════════════════════════════════════════════════════════════════════════
    // Add mega_type column to nav_items
    $has_mt = $pdo->query("SHOW COLUMNS FROM `nav_items` LIKE 'mega_type'")->fetch();
    if (!$has_mt) {
        $pdo->exec("ALTER TABLE `nav_items` ADD COLUMN `mega_type` VARCHAR(20) DEFAULT 'solutions'");
        $pdo->exec("ALTER TABLE `nav_items` ADD COLUMN `mega_img_text` VARCHAR(500) DEFAULT ''");
    }

    $new_cols = [
        ['home_content', 'why_subtitle',        "VARCHAR(300) DEFAULT ''"],
        ['home_content', 'why_text',             "TEXT DEFAULT ''"],
        ['home_content', 'solutions_text',       "TEXT DEFAULT ''"],
        ['home_content', 'automation_btn1_text', "VARCHAR(100) DEFAULT ''"],
        ['home_content', 'automation_btn2_text', "VARCHAR(100) DEFAULT ''"],
        ['home_content', 'roadmap_title',        "VARCHAR(200) DEFAULT ''"],
        ['home_content', 'roadmap_btn1_text',    "VARCHAR(100) DEFAULT ''"],
        ['home_content', 'roadmap_btn1_url',     "VARCHAR(300) DEFAULT '#'"],
        ['home_content', 'roadmap_btn2_text',    "VARCHAR(100) DEFAULT ''"],
        ['home_content', 'roadmap_btn2_url',     "VARCHAR(300) DEFAULT '#'"],
        ['home_content', 'roadmap_steps',        "TEXT DEFAULT ''"],
        ['home_content', 'cta_text',             "TEXT DEFAULT ''"],
        ['home_content', 'cta_item2',            "TEXT DEFAULT ''"],
        ['home_content', 'cta_item3',            "TEXT DEFAULT ''"],
        ['home_content', 'cta_form_title',       "VARCHAR(200) DEFAULT ''"],
        ['home_content', 'presentation_title',   "VARCHAR(200) DEFAULT ''"],
        ['home_content', 'presentation_subtitle',"VARCHAR(200) DEFAULT ''"],
        ['home_content', 'presentation_text',    "TEXT DEFAULT ''"],
        ['home_content', 'presentation_video',   "VARCHAR(500) DEFAULT ''"],
    ];
    foreach ($new_cols as [$tbl, $col, $def]) {
        $exists = $pdo->query("SHOW COLUMNS FROM `$tbl` LIKE '$col'")->fetch();
        if (!$exists) {
            $pdo->exec("ALTER TABLE `$tbl` ADD COLUMN `$col` $def");
        }
    }

    // Create home_blocks if missing
    $pdo->exec("CREATE TABLE IF NOT EXISTS home_blocks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        block_key VARCHAR(50) NOT NULL,
        label VARCHAR(100) NOT NULL,
        sort_order INT DEFAULT 0,
        is_active TINYINT DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $blocks_count = row('SELECT COUNT(*) as c FROM home_blocks')['c'];
    if ((int)$blocks_count == 0) {
        $default_blocks = [
            ['hero','Hero Section'],['why','Why Us (Carousel)'],['automation','Automation Hub'],
            ['solutions','Solutions / Tabs'],['projects','Projects Grid'],['reviews','Client Reviews'],
            ['roadmap','Roadmap'],['getintouch','Get In Touch Form'],['presentation','Video Presentation'],
        ];
        foreach ($default_blocks as $i => [$key, $label]) {
            insert('home_blocks', ['block_key'=>$key,'label'=>$label,'sort_order'=>$i,'is_active'=>1]);
        }
    }

    $done[] = 'DB migration: added missing columns and home_blocks table';

    // ══════════════════════════════════════════════════════════════════════════
    // 8. UPDATE HOME CONTENT with full text
    // ══════════════════════════════════════════════════════════════════════════
    $home = row('SELECT id FROM home_content WHERE lang_id=?', [$lid]);
    if ($home) {
        update('home_content', [
            'hero_title'          => 'Business process automation',
            'hero_subtitle'       => 'Many growing businesses lose time and money due to inefficient workflows and outdated systems. We build custom automation that eliminates manual work and scales your operations.',
            'hero_btn1_text'      => 'Try AI assistant',
            'hero_btn1_url'       => '#',
            'hero_btn2_text'      => 'Free audit',
            'hero_btn2_url'       => '#getintouch',
            'hero_video_path'     => './assets/video/1-hero.mp4',
            'why_title'           => "Why Businesses\nChoose RPA?",
            'why_subtitle'        => 'Robotic Process Automation',
            'why_text'            => 'Most SMEs can save time and money with RPA by automating tasks, reducing errors, and improving system integration.',
            'automation_title'    => 'We Build Future-Proof Automation',
            'automation_text'     => 'Many small and medium-sized enterprises encounter operational bottlenecks due to time-consuming administrative tasks, redundant data handling, and disconnected software solutions.',
            'automation_loc1_title' => 'Estonia',
            'automation_loc1_city'  => 'Tallinn',
            'automation_loc2_title' => 'Switzerland',
            'automation_loc2_city'  => 'Lausanne',
            'automation_btn1_text'  => 'Contact us',
            'automation_btn2_text'  => 'Free audit',
            'solutions_title'     => 'Automation Solutions',
            'solutions_text'      => 'As automation specialists, we offer a wide range of solutions that will be tailored to your business goals and operations.',
            'projects_title'      => "Implemented\nWorkflows",
            'projects_btn_text'   => 'View All Projects',
            'projects_btn_url'    => '/cases/',
            'reviews_title'       => 'What clients say about us',
            'cta_title'           => 'GET IN TOUCH',
            'cta_form_title'      => 'Request a free Consultation',
            'cta_text'            => "Fill out our contact form for a free consultation, or book an online meeting directly via Google Meet.",
            'cta_item2'           => 'We discuss your project even if you have just a raw idea.',
            'cta_item3'           => 'We choose a model and approach that are suitable for your case and budget.',
            'cta_btn_text'        => 'Send',
            'roadmap_title'       => 'Roadmap',
            'roadmap_btn1_text'   => 'Get PDF',
            'roadmap_btn1_url'    => '#',
            'roadmap_btn2_text'   => 'Free audit',
            'roadmap_btn2_url'    => '#getintouch',
            'presentation_subtitle' => 'Bring Ideas To Life',
            'presentation_title'  => "See RPA\nin Action",
            'presentation_text'   => 'Most SMEs waste hours each week on repetitive admin, duplicate data entry, or disjointed software systems. Sidis connects your tools so your team can focus on the work that matters.',
            'presentation_video'  => './assets/video/1-hero.mp4',
        ], ['lang_id' => $lid]);
        $done[] = 'Home content updated with full text content';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // 8b. LINK STATIC IMAGES TO CASES
    // ══════════════════════════════════════════════════════════════════════════
    $static_images = [
        'apex-agency-invoice'    => './assets/img/projects/image-1.webp',
        'nda-data-insights'      => './assets/img/projects/image-2.webp',
        'cloud-origin-extraction'=> './assets/img/projects/image-3.webp',
        'innovate-healthcare'    => './assets/img/projects/image-4.webp',
    ];
    foreach ($static_images as $slug => $img_path) {
        $case = row('SELECT id, featured_image_id FROM cases WHERE slug=?', [$slug]);
        if ($case && !$case['featured_image_id']) {
            // Check if media record already exists for this path
            $existing_media = row('SELECT id FROM media WHERE path=?', [$img_path]);
            if (!$existing_media) {
                $media_id = insert('media', [
                    'filename'      => basename($img_path),
                    'original_name' => basename($img_path),
                    'path'          => $img_path,
                    'mime_type'     => 'image/webp',
                    'file_size'     => 0,
                    'alt_text'      => '',
                ]);
            } else {
                $media_id = $existing_media['id'];
            }
            update('cases', ['featured_image_id' => $media_id], ['id' => $case['id']]);
        }
    }

    // Also add partner logos (static assets)
    $existing_logos = row('SELECT COUNT(*) as c FROM home_partner_logos')['c'];
    if ((int)$existing_logos == 0) {
        $logo_paths = [
            './assets/img/automation/partner-1.webp',
            './assets/img/automation/partner-2.webp',
            './assets/img/automation/partner-3.webp',
            './assets/img/automation/partner-4.webp',
        ];
        foreach ($logo_paths as $i => $lp) {
            $existing_media = row('SELECT id FROM media WHERE path=?', [$lp]);
            if (!$existing_media) {
                $mid = insert('media', ['filename'=>basename($lp),'original_name'=>basename($lp),'path'=>$lp,'mime_type'=>'image/webp','file_size'=>0,'alt_text'=>'Partner']);
            } else {
                $mid = $existing_media['id'];
            }
            insert('home_partner_logos', ['sort_order'=>$i,'image_id'=>$mid,'alt_text'=>'Partner']);
        }
        $done[] = 'Partner logos seeded (4 logos from static assets)';
    }

    // Automation slider images
    $existing_auto = row('SELECT COUNT(*) as c FROM home_automation_images')['c'];
    if ((int)$existing_auto == 0) {
        $auto_paths = ['./assets/img/automation/image.webp', './assets/img/promo/image-1.webp'];
        foreach ($auto_paths as $i => $ap) {
            $existing_media = row('SELECT id FROM media WHERE path=?', [$ap]);
            if (!$existing_media) {
                $mid = insert('media', ['filename'=>basename($ap),'original_name'=>basename($ap),'path'=>$ap,'mime_type'=>'image/webp','file_size'=>0,'alt_text'=>'']);
            } else {
                $mid = $existing_media['id'];
            }
            insert('home_automation_images', ['sort_order'=>$i,'image_id'=>$mid]);
        }
        $done[] = 'Automation slider images seeded';
    }

    $done[] = 'Static images linked to case studies';

    // ══════════════════════════════════════════════════════════════════════════
    // 9. SETTINGS
    // ══════════════════════════════════════════════════════════════════════════
    $settings_map = [
        'site_name'          => 'Sidis — Business Process Automation',
        'site_email'         => 'hello@sidis.agency',
        'social_linkedin'    => 'https://linkedin.com/company/sidis',
        'footer_copyright'   => '© 2025 Sidis Business Services.',
        'site_description'   => 'We build custom automation software that eliminates manual work, connects your tools, and scales your operations.',
    ];

    foreach ($settings_map as $key => $val) {
        $ex = row('SELECT id FROM settings WHERE `key`=? AND lang_id IS NULL', [$key]);
        if (!$ex) {
            insert('settings', ['key'=>$key,'value'=>$val,'lang_id'=>null]);
        }
    }
    $done[] = 'Site settings seeded';

    // ══════════════════════════════════════════════════════════════════════════
    // 10. SELF-CLEANUP — delete this file and setup.php after successful seed
    // ══════════════════════════════════════════════════════════════════════════
    $files_deleted = [];
    foreach ([__FILE__, __DIR__ . '/setup.php'] as $f) {
        if (file_exists($f)) {
            unlink($f);
            $files_deleted[] = basename($f);
        }
    }
    if ($files_deleted) {
        $done[] = 'Security cleanup: deleted ' . implode(', ', $files_deleted) . ' from server';
    }

} catch (Exception $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SIDIS — Data Seed</title>
<style>
body{font-family:system-ui,sans-serif;background:#0f0f18;color:#e0e0e0;max-width:800px;margin:40px auto;padding:0 20px}
h1{color:#772885;margin-bottom:20px}
.ok{color:#4caf50;margin:4px 0;font-size:14px}
.err{color:#f44336;margin:4px 0}
.warn{background:#772885;color:#fff;padding:16px;border-radius:8px;margin-top:24px;font-size:14px}
a{color:#a855c8}
pre{background:#1a1a2e;padding:12px;border-radius:8px;font-size:12px;overflow:auto}
</style>
</head>
<body>
<h1>SIDIS — Data Seed</h1>
<?php foreach ($errors as $e): ?><p class="err">❌ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
<?php foreach ($done as $d): ?><p class="ok">✅ <?= htmlspecialchars($d) ?></p><?php endforeach; ?>
<?php if (!$errors): ?>
<div class="warn">
    ✅ All data seeded successfully!<br><br>
    <strong>⚠ DELETE data_seed.php from the server now!</strong><br><br>
    <a href="/">→ View Site</a> &nbsp;&nbsp;
    <a href="/admin/">→ Admin Panel</a>
</div>
<?php endif; ?>
</body>
</html>
