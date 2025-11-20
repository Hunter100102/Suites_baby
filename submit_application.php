<?php
use App\Env; use App\CSRF; use App\Util;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/CSRF.php';
require_once __DIR__ . '/../app/Util.php';
Env::load(__DIR__.'/../.env');
if ($_SERVER['REQUEST_METHOD']!=='POST') { header("Location: /"); exit; }
if (Util::hp()) die("Bot detected.");
CSRF::check();

$name = Util::post('name'); $email = filter_var(Util::post('email'), FILTER_VALIDATE_EMAIL);
$phone = Util::post('phone'); $biz = Util::post('business_name');
$room = Util::post('room_number'); $ad = !empty($_POST['advertising']);
$payload = [
  'name'=>$name,'email'=>$email,'phone'=>$phone,'business_name'=>$biz,'room_number'=>$room,'advertising'=>$ad?'1':'0','advertising_price'=>'23.50'
];

$mode = Env::get('DOORLOOP_MODE','public_link');
if ($mode === 'webhook') {
  $url = Env::get('DOORLOOP_WEBHOOK_URL');
  if ($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
  }
  header("Location: /apply_success.php"); exit;
} else {
  // public link: append query params if needed
  $link = Env::get('DOORLOOP_PUBLIC_APPLY_URL','/');
  $qs = http_build_query($payload);
  header("Location: " . $link . (str_contains($link,'?') ? '&' : '?') . $qs);
  exit;
}
