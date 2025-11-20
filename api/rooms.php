<?php
use App\DB;
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/Env.php';
require_once __DIR__ . '/../../app/DB.php';
App\Env::load(__DIR__ . '/../../.env');
header('Content-Type: application/json');
$pdo = DB::pdo();
$rows = $pdo->query("SELECT room_number, status FROM rooms ORDER BY room_number")->fetchAll();
echo json_encode(['rooms'=>$rows]);
