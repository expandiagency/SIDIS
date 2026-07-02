<?php
require dirname(__DIR__) . '/includes/functions.php';
require dirname(__DIR__) . '/sendmail/phpmailer/src/Exception.php';
require dirname(__DIR__) . '/sendmail/phpmailer/src/PHPMailer.php';
require dirname(__DIR__) . '/sendmail/phpmailer/src/SMTP.php';

header('Content-Type: application/json');

$form  = $_POST['form'] ?? [];
$name     = trim($form['name']     ?? '');
$email    = trim($form['email']    ?? '');
$page_url = trim($form['page_url'] ?? '');

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

$body  = "New Contact Form Submission\n\n";
$body .= "Name:  {$name}\n";
$body .= "Email: {$email}\n";
if ($page_url) {
    $body .= "Page:  {$page_url}\n";
}

$results = [];
$error   = '';

try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'services@sidis.group';
    $mail->Password   = 'rjsewiijqurgojme';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('services@sidis.group', 'SIDIS Website');
    $mail->addReplyTo($email, $name);
    $mail->Subject = 'New Lead from SIDIS website';
    $mail->Body    = $body;

    foreach ($recipients as $recipient) {
        $mail->clearAddresses();
        $mail->addAddress($recipient);
        $mail->send();
        $results[] = $recipient . ':ok';
    }
} catch (Exception $e) {
    $error   = $mail->ErrorInfo;
    $results[] = 'error:' . $error;
}

$log_line = date('Y-m-d H:i:s') . ' | ' . $name . ' | ' . $email . ' | ' . implode(', ', $results) . ($error ? ' | ERR: ' . $error : '') . "\n";
file_put_contents(dirname(__DIR__) . '/sendmail/mail.log', $log_line, FILE_APPEND | LOCK_EX);

echo json_encode(['message' => 'Дані надіслані!', 'debug' => $results]);
