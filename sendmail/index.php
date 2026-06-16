<?php
require dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

$form  = $_POST['form'] ?? [];
$name  = trim($form['name']  ?? '');
$email = trim($form['email'] ?? '');

if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['message' => 'Error: invalid data']);
    exit;
}

$raw        = get_setting('lead_emails') ?: 'services@sidis.group,hello@expandi.agency';
$recipients = array_filter(array_map('trim', explode(',', $raw)));

if (empty($recipients)) {
    echo json_encode(['message' => 'Error: no recipients configured']);
    exit;
}

$subject = '=?UTF-8?B?' . base64_encode('New Lead from SIDIS website') . '?=';

$body  = "New Contact Form Submission\n\n";
$body .= "Name:  {$name}\n";
$body .= "Email: {$email}\n";

$headers  = "From: noreply@sidistech.group\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

foreach ($recipients as $recipient) {
    mail($recipient, $subject, $body, $headers);
}

echo json_encode(['message' => 'Дані надіслані!']);
