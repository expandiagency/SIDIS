<?php
require __DIR__ . '/config.php';
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

// Load SMTP settings from DB
$smtp_host   = get_setting('smtp_host');
$smtp_user   = get_setting('smtp_user');
$smtp_pass   = get_setting('smtp_pass');
$smtp_port   = (int)(get_setting('smtp_port') ?: 587);
$smtp_secure = get_setting('smtp_secure') ?: 'tls';
$from_email  = get_setting('smtp_from_email') ?: (get_setting('site_email') ?: 'noreply@sidistech.group');
$from_name   = get_setting('smtp_from_name') ?: (get_setting('site_name') ?: 'SIDIS Website');

use PHPMailer\PHPMailer\PHPMailer;

try {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->setLanguage('en', __DIR__ . '/phpmailer/language/');
    $mail->isHTML(true);

    if ($smtp_host && $smtp_user) {
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_user;
        $mail->Password   = $smtp_pass;
        $mail->SMTPSecure = $smtp_secure === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : ($smtp_secure === 'none' ? '' : PHPMailer::ENCRYPTION_STARTTLS);
        $mail->Port       = $smtp_port;
    }

    $mail->setFrom($from_email, $from_name);
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
} catch (\Exception $e) {
    echo json_encode(['message' => 'Error: ' . ($mail->ErrorInfo ?: $e->getMessage())]);
}
