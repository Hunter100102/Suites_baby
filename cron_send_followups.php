<?php
use App\Env; use App\DB;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
Env::load(__DIR__.'/../.env');
$pdo = DB::pdo();

$today = date('Y-m-d');
$rows = $pdo->prepare("SELECT * FROM appointments WHERE preferred_date=? AND followup_sent=0");
$rows->execute([$today]);
$items = $rows->fetchAll();

if (!$items) { echo "No followups."; exit; }

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
$mail = new PHPMailer(true);
foreach ($items as $ap) {
  try {
    $mail->isSMTP();
    $mail->Host = Env::get('SMTP_HOST');
    $mail->SMTPAuth = true;
    $mail->Username = Env::get('SMTP_USER');
    $mail->Password = Env::get('SMTP_PASS');
    $mail->SMTPSecure = Env::get('SMTP_SECURE','tls');
    $mail->Port = (int)Env::get('SMTP_PORT',587);
    $mail->setFrom(Env::get('SMTP_FROM_EMAIL'), Env::get('SMTP_FROM_NAME','Aban Suites'));
    $mail->addAddress($ap['email'], $ap['name']);
    $mail->isHTML(true);
    $mail->Subject = "Liked the suite you viewed? Continue your application.";
    $url = rtrim(Env::get('APP_URL','https://abansuites.com'),'/') . "/apply.php?appointment_id=" . $ap['id'];
    $mail->Body = "Hi {$ap['name']},<br><br>If you liked the suite(s) you viewed, please click below to continue.<br><br><a href=\"$url\">Continue Application</a><br><br>— Aban Suites";
    $mail->AltBody = "If you liked the suite(s) you viewed, continue here: $url";
    $mail->send();
    $pdo->prepare("UPDATE appointments SET followup_sent=1 WHERE id=?")->execute([$ap['id']]);
  } catch (\Throwable $e) {
    error_log("Followup email error: " . $e->getMessage());
  }
}
echo "Done";
