<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PHPMailer/Exception.php';
require_once __DIR__ . '/../includes/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

setCors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

/* ── Honeypot — silent drop to fool bots ────────────────── */
if (!empty($_POST['botcheck'])) {
    jsonOut(['success' => true]);
}

/* ── Collect & sanitise ─────────────────────────────────── */
$name    = clean($_POST['name']          ?? '');
$biz     = clean($_POST['business_name'] ?? '');
$phone   = clean($_POST['phone']         ?? '');
$email   = clean($_POST['email']         ?? '');
$industry = clean($_POST['industry']     ?? '');
$role    = clean($_POST['role']           ?? '');
$message = clean($_POST['message']       ?? '');

/* ── Server-side validation ─────────────────────────────── */
$errors = [];
if (!$name)                         $errors[] = 'Name is required.';
if (!$phone)                        $errors[] = 'WhatsApp number is required.';
if (!$email)                        $errors[] = 'Email is required.';
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';

if ($errors) {
    jsonError(implode(' ', $errors), 422);
}

/* ── Build HTML email body ──────────────────────────────── */
$rows = [
    'Full Name'        => $name,
    'Business Name'    => $biz    ?: '—',
    'WhatsApp Number'  => $phone,
    'Email'            => $email,
    'Industry'         => $industry ?: '—',
    'Role'             => $role     ?: '—',
    'Message'          => $message  ? nl2br(htmlspecialchars($message)) : '—',
];

$tableRows = '';
foreach ($rows as $label => $val) {
    $tableRows .= "
      <tr>
        <td style='padding:10px 16px;font-weight:600;color:#475569;background:#F8FAFC;
                   border:1px solid #E2E8F0;white-space:nowrap;font-size:13px;'>{$label}</td>
        <td style='padding:10px 16px;color:#0F172A;border:1px solid #E2E8F0;font-size:14px;'>{$val}</td>
      </tr>";
}

$subject = "New Demo Request – {$biz}";
$body    = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='margin:0;padding:0;font-family:Inter,Arial,sans-serif;background:#F1F5F9;'>
  <div style='max-width:600px;margin:40px auto;background:#fff;border-radius:12px;
              overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);'>
    <div style='background:linear-gradient(135deg,#25D366,#128C7E);padding:32px 36px;'>
      <h1 style='margin:0;color:#fff;font-size:20px;font-weight:600;'>
        New Demo Request
      </h1>
      <p style='margin:6px 0 0;color:rgba(255,255,255,.85);font-size:14px;'>
        Submitted via Whatapi contact form
      </p>
    </div>
    <div style='padding:32px 36px;'>
      <table style='width:100%;border-collapse:collapse;'>
        {$tableRows}
      </table>
    </div>
    <div style='padding:20px 36px;border-top:1px solid #E2E8F0;background:#FAFAFA;'>
      <p style='margin:0;font-size:12px;color:#94A3B8;'>
        Reply directly to this email to respond to {$name}.
      </p>
    </div>
  </div>
</body>
</html>";

/* ── Send via PHPMailer ──────────────────────────────────── */
try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_PORT === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(SMTP_USER, 'Whatapi');
    $mail->addAddress(CONTACT_TO);
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $body));

    $mail->send();
    jsonOut(['success' => true]);

} catch (Exception $e) {
    if (DEBUG) jsonError('Mailer error: ' . $mail->ErrorInfo, 500);
    jsonError('Could not send email. Please try again or reach us on WhatsApp.', 500);
}
