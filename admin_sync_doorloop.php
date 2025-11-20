<?php
use App\DB; use App\Auth; use App\CSRF;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/CSRF.php';
require_once __DIR__ . '/../integrations/doorloop.php';
App\Env::load(__DIR__.'/../.env');
Auth::start();
$u = Auth::user();
if (!$u || $u['role']!=='admin') { header("Location: /login.php"); exit; }
$pdo = DB::pdo();
$id = intval($_GET['tenant_id'] ?? 0);
$st = $pdo->prepare("SELECT t.*, u.email, u.phone FROM tenants t LEFT JOIN users u ON t.user_id=u.id WHERE t.id=?");
$st->execute([$id]);
$tenant = $st->fetch(PDO::FETCH_ASSOC);
if (!$tenant) { http_response_code(404); echo "Not found"; exit; }
$res = Integrations\DoorLoop::pushTenant($tenant);
header("Content-Type: application/json");
echo json_encode($res);
