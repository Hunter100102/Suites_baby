<?php
use App\DB; use App\Auth; use App\CSRF;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/CSRF.php';
App\Env::load(__DIR__.'/../.env');
Auth::start();
$u = Auth::user();
if (!$u || $u['role']!=='admin') { header("Location: /login.php"); exit; }
$pdo = DB::pdo();
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); exit; }
$csrf = $_POST['csrf'] ?? '';
if (!CSRF::check($csrf)) { http_response_code(400); echo "bad csrf"; exit; }
$tenant_id = intval($_POST['tenant_id'] ?? 0);
$room = intval($_POST['room_number'] ?? 0);
if ($tenant_id<=0 || $room<=0 || $room>20) { http_response_code(400); echo "bad params"; exit; }
$pdo->exec("ALTER TABLE tenants ADD COLUMN IF NOT EXISTS room_number INT NULL");
$pdo->prepare("UPDATE tenants SET room_number=? WHERE id=?")->execute([$room,$tenant_id]);
$pdo->prepare("UPDATE rooms SET status='occupied' WHERE room_number=?")->execute([$room]);
header("Location: /admin.php#tenant-" . $tenant_id);
