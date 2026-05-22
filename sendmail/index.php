<?php
require 'config.php';
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
    echo json_encode(['message' => 'Error: no recipients']);
    exit;
}

try {
    $mail->setFrom('noreply@sidis.expandi.agency', 'SIDIS Website');
    $mail->addReplyTo($email, $name);
    $mail->Subject = 'New Lead from SIDIS website';
    $mail->Body    = '<h2>New Contact Form Submission</h2>'
                   . '<p><strong>Name:</strong> '  . htmlspecialchars($name,  ENT_QUOTES) . '</p>'
                   . '<p><strong>Email:</strong> <a href="mailto:' . htmlspecialchars($email, ENT_QUOTES) . '">' . htmlspecialchars($email, ENT_QUOTES) . '</a></p>';
    $mail->AltBody = "New Lead from SIDIS website\n\nName: $name\nEmail: $email";

    foreach ($recipients as $recipient) {
        $mail->addAddress($recipient);
    }

    $mail->send();
    echo json_encode(['message' => 'Дані надіслані!']);
} catch (Exception $e) {
    echo json_encode(['message' => 'Error: ' . $mail->ErrorInfo]);
}
