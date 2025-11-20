<?php
use App\Auth; use App\DB; use App\CSRF; use App\Util; use App\Env;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/CSRF.php';
require_once __DIR__ . '/../app/Util.php';
Env::load(__DIR__.'/../.env');
Auth::start();
$pdo = DB::pdo();

// Bootstrap admin on first run
//$adminEmail = Env::get('ADMIN_EMAIL');
//$adminPass = Env::get('ADMIN_PASSWORD');
//if ($adminEmail && $adminPass) {
//  $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
//  $stmt->execute([$adminEmail]);
//  if (!$stmt->fetch()) {
//    $ins = $pdo->prepare("INSERT INTO users (email, password_hash, role, name) VALUES (?,?, 'admin', 'Administrator')");
//    $ins->execute([$adminEmail, password_hash($adminPass, PASSWORD_DEFAULT)]);
//  }
//}

$u = Auth::user();
if (!$u || $u['role']!=='admin') { header("Location: /login.php"); exit; }

$msg = "";
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (Util::hp()) die("Bot detected.");
  CSRF::check();
  if (isset($_POST['toggle_room'])) {
    $rn = (int)Util::post('room_number');
    $st = $pdo->prepare("UPDATE rooms SET status = IF(status='available','unavailable','available') WHERE room_number=?");
    $st->execute([$rn]);
    $msg = "Room $rn status toggled.";
  }
  if (isset($_POST['create_tenant'])) {
    $name = Util::post('name');
    $email = filter_var(Util::post('email'), FILTER_VALIDATE_EMAIL);
    $pass = Util::post('password');
    $biz = Util::post('business_name');
    if ($name && $email && $pass && $biz) {
      $pdo->prepare("INSERT INTO users (email,password_hash,role,name) VALUES (?,?, 'tenant', ?)")
        ->execute([$email, password_hash($pass, PASSWORD_DEFAULT), $name]);
      $uid = $pdo->lastInsertId();
      $pdo->prepare("INSERT INTO tenants (user_id,business_name) VALUES (?,?)")->execute([$uid,$biz]);
      $msg = "Tenant created.";
    } else { $msg = "Fill all fields."; }
  }
}

$rooms = $pdo->query("SELECT * FROM rooms ORDER BY room_number")->fetchAll();
$tenants = $pdo->query("SELECT t.*, u.email FROM tenants t JOIN users u ON t.user_id=u.id ORDER BY t.business_name")->fetchAll();
?>
<?php $title="Admin — Aban Suites"; include __DIR__ . "/partials_header.php"; ?>
<div class="container card">
  <?php if ($msg): ?><p class="notice"><?= App\Util::e($msg) ?></p><?php endif; ?>
  <h2>Rooms</h2>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
    <?php foreach ($rooms as $r): ?>
      <form method="POST" class="card" style="padding:10px;">
        <input type="hidden" name="confirm_bot" value="">
        <?= App\CSRF::field() ?>
        <input type="hidden" name="toggle_room" value="1">
        <input type="hidden" name="room_number" value="<?= (int)$r['room_number'] ?>">
        <div>Room <?= (int)$r['room_number'] ?> — <strong><?= App\Util::e($r['status']) ?></strong></div>
        <button class="btn" type="submit">Toggle</button>
      </form>
    <?php endforeach; ?>
  </div>

  <h2 style="margin-top:24px;">Create Tenant</h2>
  <form method="POST">
    <input type="hidden" name="confirm_bot" value="">
    <?= App\CSRF::field() ?>
    <input type="hidden" name="create_tenant" value="1">
    <div class="form-row two">
      <div><label>Name</label><input type="text" name="name" required></div>
      <div><label>Email</label><input type="email" name="email" required></div>
    </div>
    <div class="form-row two">
      <div><label>Password</label><input type="password" name="password" required></div>
      <div><label>Business Name</label><input type="text" name="business_name" required></div>
    </div>
    <button class="btn cta" type="submit">Create</button>
  </form>
</div>
<div class="container card" style="margin-top:1rem;">
  <a class="btn" href="/admin_create_tenant.php">Create Tenant & Assign Room</a>
</div>
<?php include __DIR__ . "/partials_footer.php"; ?>
