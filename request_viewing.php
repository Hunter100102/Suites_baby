<?php
use App\Env; use App\DB; use App\CSRF; use App\Util;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/CSRF.php';
require_once __DIR__ . '/../app/Util.php';
Env::load(__DIR__.'/../.env');
if ($_SERVER['REQUEST_METHOD']!=='POST') { header("Location: /"); exit; }
if (Util::hp()) die("Bot detected.");
CSRF::check();
$pdo = DB::pdo();

$name = Util::post('name'); $email = filter_var(Util::post('email'), FILTER_VALIDATE_EMAIL);
$phone = Util::post('phone'); $business = Util::post('business_name');
$room_number = (int)Util::post('room_number'); $preferred_date = Util::post('preferred_date');
if (!$name || !$email || !$phone || !$business || !$room_number || !$preferred_date) {
  die("Missing fields.");
}

$stmt = $pdo->prepare("INSERT INTO appointments (name,email,phone,business_name,room_number,preferred_date) VALUES (?,?,?,?,?,?)");
$stmt->execute([$name,$email,$phone,$business,$room_number,$preferred_date]);

// Send SMS via Textbelt
$apiKey = Env::get('TEXTBELT_API_KEY');
$to = Env::get('TEXTBELT_TO_NUMBER', '+16782627635');
if ($apiKey) {
  $msg = "New viewing request: $name, $phone, $email, Business: $business, Room $room_number, Date: $preferred_date";
  $ch = curl_init("https://textbelt.com/text");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'phone' => $to,
    'message' => $msg,
    'key' => $apiKey
  ]));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $res = curl_exec($ch);
  curl_close($ch);
}

header("Location: /thankyou.php");
