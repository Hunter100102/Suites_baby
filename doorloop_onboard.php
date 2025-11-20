<?php
use App\DB; use App\Util;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/Util.php';

App\Env::load(__DIR__.'/../.env');
$pdo = DB::pdo();

$token = $_GET['token'] ?? '';
$st = $pdo->prepare("SELECT t.* FROM onboarding_tokens ot JOIN tenants t ON t.id=ot.tenant_id WHERE ot.token=?");
$st->execute([$token]);
$tenant = $st->fetch(PDO::FETCH_ASSOC);
if (!$tenant) { http_response_code(404); echo "Invalid link"; exit; }

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $biz = Util::post('business_name'); $services = Util::post('services'); $bio = Util::post('bio');
  $website = Util::post('website'); $ein = Util::post('ein'); $legal = Util::post('legal_name');
  $upd = $pdo->prepare("UPDATE tenants SET business_name=?, services=?, bio=?, website=?, ein=?, legal_name=? WHERE id=?");
  $upd->execute([$biz,$services,$bio,$website,$ein,$legal,$tenant['id']]);
  header("Location:/thankyou.php"); exit;
}
?>
<?php $title="Complete Your Profile"; include __DIR__ . "/partials_header.php"; ?>
<div class="container card">
  <h2>Complete Your Profile</h2>
  <p>This info helps management add you to DoorLoop and the building directory.</p>
  <form method="post">
    <div class="form-row two">
      <div><label>Business Name</label><input name="business_name" value="<?= htmlspecialchars($tenant['business_name'] ?? '') ?>" required></div>
      <div><label>Legal Name</label><input name="legal_name" value="<?= htmlspecialchars($tenant['legal_name'] ?? '') ?>"></div>
    </div>
    <div class="form-row two">
      <div><label>Website</label><input name="website" placeholder="https://"></div>
      <div><label>EIN (optional)</label><input name="ein"></div>
    </div>
    <div><label>Services</label><input name="services" value="<?= htmlspecialchars($tenant['services'] ?? '') ?>"></div>
    <div><label>Short Bio</label><textarea name="bio" rows="4"></textarea></div>
    <button class="btn" type="submit">Save</button>
  </form>
</div>
<?php include __DIR__ . "/partials_footer.php"; ?>
