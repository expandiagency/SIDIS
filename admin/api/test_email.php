<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$to   = trim($data['to'] ?? get_setting('lead_emails'));
$to   = trim(explode(',', $to)[0]); // use first recipient for test

if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
    json_response(['error' => 'No valid recipient — fill in Lead Notification Emails first']);
}

require_once dirname(dirname(__DIR__)) . '/sendmail/config.php';

use PHPMailer\PHPMailer\PHPMailer;

try {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->setLanguage('en', dirname(dirname(__DIR__)) . '/sendmail/phpmailer/language/');
    $mail->isHTML(true);

    $smtp_host    = get_setting('smtp_host');
    $smtp_user    = get_setting('smtp_user');
    $smtp_pass    = get_setting('smtp_pass');
    $smtp_port    = (int)(get_setting('smtp_port') ?: 587);
    $smtp_secure  = get_setting('smtp_secure') ?: 'tls';
    $from_email   = get_setting('smtp_from_email') ?: (get_setting('site_email') ?: 'noreply@sidistech.group');
    $from_name    = get_setting('smtp_from_name') ?: (get_setting('site_name') ?: 'SIDIS Website');

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
    $mail->addAddress($to);
    $mail->Subject = 'SIDIS CMS — Test Email';
    $mail->Body    = '<h2>Test email from SIDIS CMS</h2><p>SMTP is configured correctly. Notifications from contact forms will arrive at this address.</p>';
    $mail->AltBody = 'Test email from SIDIS CMS. SMTP is configured correctly.';
    $mail->send();

    json_response(['ok' => true, 'sent_to' => $to]);
} catch (\Exception $e) {
    json_response(['error' => $mail->ErrorInfo ?: $e->getMessage()]);
}
