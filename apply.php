<?php
use App\Env; use App\DB; use App\Util; use App\CSRF;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/Util.php';
require_once __DIR__ . '/../app/CSRF.php';
Env::load(__DIR__.'/../.env');
$pdo = DB::pdo();
$appointment_id = (int)($_GET['appointment_id'] ?? 0);
$ap = null;
if ($appointment_id) {
  $st = $pdo->prepare("SELECT * FROM appointments WHERE id=?");
  $st->execute([$appointment_id]);
  $ap = $st->fetch();
}
$title="Apply — Aban Suites"; include __DIR__ . "/partials_header.php";
?>
<div class="container card">
  <h2>Application</h2>
  <p>Complete the short form to proceed. Advertisement package is selected by default (you can remove it).</p>
  <form method="POST" action="/submit_application.php">
    <input type="hidden" name="confirm_bot" value="">
    <?= App\CSRF::field() ?>
    <input type="hidden" name="appointment_id" value="<?= (int)$appointment_id ?>">
    <div class="form-row two">
      <div><label>Name</label><input type="text" name="name" value="<?= App\Util::e($ap['name'] ?? '') ?>" required></div>
      <div><label>Email</label><input type="email" name="email" value="<?= App\Util::e($ap['email'] ?? '') ?>" required></div>
    </div>
    <div class="form-row two">
      <div><label>Phone</label><input type="text" name="phone" value="<?= App\Util::e($ap['phone'] ?? '') ?>" required></div>
      <div><label>Business Name</label><input type="text" name="business_name" value="<?= App\Util::e($ap['business_name'] ?? '') ?>" required></div>
    </div>
    <div class="form-row two">
      <div><label>Room #</label><input type="text" name="room_number" value="<?= App\Util::e($ap['room_number'] ?? '') ?>" required></div>
      <div><label>Advertisement Package ($23.50/mo)</label><input type="checkbox" name="advertising" value="1" checked></div>
    </div>
    <button class="btn cta" type="submit">Continue</button>
  </form>
</div>
<?php include __DIR__ . "/partials_footer.php"; ?>
