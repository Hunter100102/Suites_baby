<?php
use App\Auth; use App\DB; use App\CSRF; use App\Util;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/CSRF.php';
require_once __DIR__ . '/../app/Util.php';
App\Env::load(__DIR__.'/../.env');
Auth::start();
$u = Auth::user();
if (!$u || $u['role']!=='tenant') { header("Location: /login.php"); exit; }
$pdo = DB::pdo();

// Grab or create tenant profile
$st = $pdo->prepare("SELECT * FROM tenants WHERE user_id=?");
$st->execute([$u['id']]);
$tenant = $st->fetch();
if (!$tenant) {
  $ins = $pdo->prepare("INSERT INTO tenants (user_id, business_name) VALUES (?, ?)");
  $ins->execute([$u['id'], $u['name'] ?: 'My Business']);
  $st->execute([$u['id']]);
  $tenant = $st->fetch();
}

$notice = "";
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (Util::hp()) die("Bot detected.");
  CSRF::check();
  if (isset($_POST['update_profile'])) {
    $biz = Util::post('business_name');
    $services = Util::post('services');
    $bio = Util::post('bio');
    $upd = $pdo->prepare("UPDATE tenants SET business_name=?, services=?, bio=? WHERE id=?");
    $upd->execute([$biz,$services,$bio,$tenant['id']]);
    // Avatar upload
    if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error']===UPLOAD_ERR_OK) {
      $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
      if (in_array($ext, ['jpg','jpeg','png','webp'])) {
        $dir = __DIR__ . '/uploads/avatars';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $dest = $dir . '/' . $tenant['id'] . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $dest);
        $rel = str_replace(__DIR__, '', $dest);
        $rel = '/uploads/avatars/' . basename($dest);
        $pdo->prepare("UPDATE tenants SET avatar=? WHERE id=?")->execute([$rel, $tenant['id']]);
      }
    }
    $notice = "Profile updated.";
  }
  if (isset($_POST['upload_photo'])) {
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error']===UPLOAD_ERR_OK) {
      $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
      if (in_array($ext, ['jpg','jpeg','png','webp'])) {
        $dir = __DIR__ . '/uploads/tenant_photos';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $dest = $dir . '/' . $tenant['id'] . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $dest);
        $rel = '/uploads/tenant_photos/' . basename($dest);
        $cap = Util::post('caption');
        $pdo->prepare("INSERT INTO tenant_photos (tenant_id, file_path, caption) VALUES (?,?,?)")->execute([$tenant['id'],$rel,$cap]);
        $notice = "Photo uploaded.";
      }
    }
  }
  // refresh
  $st->execute([$u['id']]);
  $tenant = $st->fetch();
}
?>
<?php $title="Tenant Dashboard — Aban Suites"; include __DIR__ . "/partials_header.php"; ?>
<div class="container card">
  <div class="notice" id="referral-banner"><?= App\Env::get('REFERRAL_BONUS_TEXT', '$200 for a referral leading to a new tenant') ?></div>
  <?php if ($notice): ?><p class="notice"><?= App\Util::e($notice) ?></p><?php endif; ?>
  <h2>Edit Profile</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="confirm_bot" value="">
    <?= App\CSRF::field() ?>
    <input type="hidden" name="update_profile" value="1">
    <div class="form-row two">
      <div><label>Business Name</label><input type="text" name="business_name" value="<?= App\Util::e($tenant['business_name']) ?>" required></div>
    </div>
    <div><label>Services / Prices</label><textarea name="services" rows="4"><?= App\Util::e($tenant['services'] ?? '') ?></textarea></div>
    <div><label>Bio</label><textarea name="bio" rows="3"><?= App\Util::e($tenant['bio'] ?? '') ?></textarea></div>
    <div><label>Avatar</label><input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp"></div>
    <button class="btn cta" type="submit">Save</button>
  </form>

  <h3 style="margin-top:24px;">Example Photos</h3>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="confirm_bot" value="">
    <?= App\CSRF::field() ?>
    <input type="hidden" name="upload_photo" value="1">
    <div class="form-row two">
      <div><label>Photo</label><input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" required></div>
      <div><label>Caption</label><input type="text" name="caption"></div>
    </div>
    <button class="btn" type="submit">Upload</button>
  </form>
</div>
<script>
  // show referral modal/banner on load
  window.addEventListener('load', () => {
    const b = document.getElementById('referral-banner');
    b.style.display = 'block';
  });
</script>
<?php include __DIR__ . "/partials_footer.php"; ?>
