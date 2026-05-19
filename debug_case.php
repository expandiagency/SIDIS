<?php
require_once __DIR__ . '/includes/functions.php';
echo '<pre>';

$case = row(
    'SELECT c.*, ct.title, ct.location,
            logo.path as logo_path, img.path as image_path
     FROM cases c
     LEFT JOIN cases_t ct ON c.id=ct.case_id AND ct.lang_id=1
     LEFT JOIN media logo ON c.company_logo_id=logo.id
     LEFT JOIN media img ON c.featured_image_id=img.id
     WHERE c.slug=?',
    ['apex-agency-invoice']
);

echo "case id: " . ($case['id'] ?? 'NULL') . "\n";
echo "company_logo_id: " . ($case['company_logo_id'] ?? 'NULL') . "\n";
echo "featured_image_id: " . ($case['featured_image_id'] ?? 'NULL') . "\n";
echo "logo_path: " . ($case['logo_path'] ?? 'NULL') . "\n";
echo "image_path: " . ($case['image_path'] ?? 'NULL') . "\n";
echo "media_url(image_path): " . media_url($case['image_path'] ?? '') . "\n";
echo "media_url(logo_path): " . media_url($case['logo_path'] ?? '') . "\n";

// Check media records
if ($case['company_logo_id']) {
    $m = row('SELECT * FROM media WHERE id=?', [$case['company_logo_id']]);
    echo "\nLogo media record: "; print_r($m);
}
if ($case['featured_image_id']) {
    $m = row('SELECT * FROM media WHERE id=?', [$case['featured_image_id']]);
    echo "\nImage media record: "; print_r($m);
}

// Check columns in cases table
$cols = rows('SHOW COLUMNS FROM cases');
echo "\ncases table columns:\n";
foreach ($cols as $col) echo "  " . $col['Field'] . " (" . $col['Type'] . ")\n";

echo '</pre>';
unlink(__FILE__);
echo '<p style="color:green;font-family:monospace">Debug script deleted.</p>';
