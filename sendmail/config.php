<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->setLanguage('en', 'phpmailer/language/');
$mail->isHTML(true);

// ── SMTP (uncomment and fill in if PHP mail() doesn't work) ──────────────────
// $mail->isSMTP();
// $mail->Host       = 'mail.yourdomain.com'; // or smtp.gmail.com / smtp.mailgun.org
// $mail->SMTPAuth   = true;
// $mail->Username   = 'noreply@yourdomain.com';
// $mail->Password   = 'your_password';
// $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
// $mail->Port       = 465;
