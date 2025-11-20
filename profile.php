<?php
// profile.php — Tenant public profile

use App\Env;
use App\DB;
use App\Auth;
use App\Util;

$root = dirname(__DIR__);

// Bootstrap (same pattern as rate_tenant.php, book.php, etc.)
require_once $root . '/vendor/autoload.php';
require_once $root . '/app/Env.php';
require_once $root . '/app/DB.php';
require_once $root . '/app/Auth.php';
require_once $root . '/app/CSRF.php';
require_once $root . '/app/Util.php';

App\Env::load($root . '/.env');
App\Auth::start();

$title = "Tenant Profile — Aban Suites";
$pdo   = DB::pdo();

// Accept tenant id via ?tenant_id= or ?id=
$tenantId = (int)($_GET['tenant_id'] ?? $_GET['id'] ?? 0);
$tenant   = null;

if ($tenantId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM tenants WHERE id = ? LIMIT 1");
    $stmt->execute([$tenantId]);
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch recent reviews from `ratings` table
$reviews = [];
if ($tenant) {
    $stmt = $pdo->prepare("
        SELECT score, author_name, comment, created_at
        FROM ratings
        WHERE tenant_id = ?
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$tenantId]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include __DIR__ . '/partials_header.php';

if (!$tenant): ?>
  <div class="container card">
    <h2>Tenant not found</h2>
    <p>The profile you requested could not be located.</p>
    <a class="btn sm" href="/tenants.php">← Back to Tenants</a>
  </div>
  <?php include __DIR__ . '/partials_footer.php'; exit; ?>
<?php endif; ?>

<?php
// helper
$col = function(array $row, string $key): string {
    return isset($row[$key]) ? trim((string)$row[$key]) : '';
};

$businessName = $col($tenant, 'business_name') ?: 'Business';
$bio          = $col($tenant, 'bio');
$roomNumber   = $col($tenant, 'room_number'); // can be empty, that’s fine
$avatar       = $col($tenant, 'avatar');
$logoPath     = $avatar !== '' ? $avatar : '/assets/img/avatar-placeholder.jpg';

// ratings from tenants table
$ratingAvg   = $tenant['rating_avg']   ?? null;
$ratingCount = isset($tenant['rating_count']) ? (int)$tenant['rating_count'] : 0;
if ($ratingAvg === null && isset($tenant['rating'])) {
    $ratingAvg = $tenant['rating'];
}
?>
<style>
.profile-hero {
  position:relative;
  width:100%;
  height:240px;
  border-radius:16px;
  overflow:hidden;
  background:#eee;
  margin-bottom:1rem;
}
.profile-hero img { width:100%; height:100%; object-fit:cover; }

.profile-header {
  display:grid;
  grid-template-columns:96px 1fr;
  gap:1rem;
  align-items:center;
  margin-bottom:1rem;
}
.profile-logo {
  width:96px; height:96px;
  border-radius:12px;
  overflow:hidden;
  background:#fafafa;
  border:1px solid #eee;
}
.profile-logo img { width:100%; height:100%; object-fit:cover; }

.badge {
  display:inline-block;
  padding:.25rem .5rem;
  border-radius:999px;
  font-size:.85rem;
  background:#f2f2f2;
  color:#333;
  border:1px solid #e5e5e5;
}
.badge.rating { color:#b45309; }

.action-row {
  display:flex;
  flex-wrap:wrap;
  gap:.5rem;
  margin:.5rem 0 1rem;
}
.btn.primary { background:#111; color:#fff; border:1px solid #111; }
.btn.ghost   { background:#fff; color:#111; border:1px solid #ddd; }
.btn {
  padding:.6rem .9rem;
  border-radius:10px;
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  gap:.4rem;
}

.section { margin-top:1.25rem; }
hr.soft {
  border:0;
  border-top:1px solid #eee;
  margin:1.25rem 0;
}

.review {
  padding:.6rem .7rem;
  border-radius:10px;
  border:1px solid #eee;
  margin-bottom:.5rem;
  background:#fafafa;
}
.review .stars {
  color:#f1b200;
  font-weight:600;
  margin-bottom:.25rem;
}
.review small { color:#6b7280; }

@media (max-width:640px){
  .profile-header { grid-template-columns:72px 1fr; }
  .profile-logo   { width:72px; height:72px; }
}
</style>

<div class="container card">

  <?php if (!empty($_GET['booked'])): ?>
    <div class="notice success" style="
        margin-bottom:12px;
        padding:10px 12px;
        border-radius:10px;
        background:#ecfdf3;
        border:1px solid #bbf7d0;
        color:#166534;
        font-size:14px;">
      Your booking request was sent. The tenant will reach out to confirm your appointment.
    </div>
  <?php endif; ?>


  <div class="profile-header">
    <div class="profile-logo">
      <img src="<?= Util::e($logoPath) ?>" alt="<?= Util::e($businessName) ?> logo">
    </div>
    <div>
      <h2 style="margin:.25rem 0 .35rem"><?= Util::e($businessName) ?></h2>
      <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
        <?php if ($roomNumber !== ''): ?>
          <span class="badge">Room <?= Util::e($roomNumber) ?></span>
        <?php endif; ?>
        <?php if ($ratingAvg !== null): ?>
          <span class="badge rating">
            ★ <?= Util::e(number_format((float)$ratingAvg, 1)) ?>
            <?php if ($ratingCount > 0): ?>
              (<?= $ratingCount ?>)
            <?php endif; ?>
          </span>
        <?php else: ?>
          <span class="badge">Not yet rated</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="action-row">
    <a class="btn primary" href="/book.php?tenant_id=<?= (int)$tenantId ?>">Book Appointment</a>
    <a class="btn ghost" href="/rate_tenant.php?tenant_id=<?= (int)$tenantId ?>">Rate this tenant</a>
    <a class="btn ghost" href="/tenants.php">← Back to Tenants</a>
  </div>

  <?php if ($bio !== ''): ?>
    <div class="section">
      <h3>About</h3>
      <p style="white-space:pre-line"><?= Util::e($bio) ?></p>
    </div>
    <hr class="soft">
  <?php endif; ?>

  <?php if ($reviews): ?>
    <div class="section" id="reviews">
      <h3>Recent Reviews</h3>
      <?php foreach ($reviews as $rev): ?>
        <div class="review">
          <div class="stars">
            <?php for ($i = 0; $i < (int)$rev['score']; $i++): ?>★<?php endfor; ?>
            <?php for ($i = (int)$rev['score']; $i < 5; $i++): ?>☆<?php endfor; ?>
          </div>
          <?php if (!empty($rev['author_name'])): ?>
            <strong><?= Util::e($rev['author_name']) ?></strong><br>
          <?php endif; ?>
          <?php if (!empty($rev['comment'])): ?>
            <div><?= Util::e($rev['comment']) ?></div>
          <?php endif; ?>
          <?php if (!empty($rev['created_at'])): ?>
            <small><?= Util::e($rev['created_at']) ?></small>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials_footer.php'; ?>
