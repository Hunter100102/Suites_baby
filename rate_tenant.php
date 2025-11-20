<?php
// rate_tenant.php — Show + handle rating form for a tenant

use App\Env;
use App\DB;
use App\Auth;
use App\CSRF;
use App\Util;

$root = dirname(__DIR__);

// Core bootstrap
require_once $root . '/vendor/autoload.php';
require_once $root . '/app/Env.php';
require_once $root . '/app/DB.php';
require_once $root . '/app/Auth.php';
require_once $root . '/app/CSRF.php';
require_once $root . '/app/Util.php';

App\Env::load($root . '/.env');
App\Auth::start();

$pdo = DB::pdo();

// ---------- Handle POST (save rating) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    App\CSRF::check();

    $tenant_id    = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
    $score        = isset($_POST['score']) ? (int)$_POST['score'] : 0;
    $author_name  = trim($_POST['author_name']  ?? '');
    $author_email = trim($_POST['author_email'] ?? '');
    $comment      = trim($_POST['comment']      ?? '');

    if ($tenant_id <= 0 || $score < 1 || $score > 5) {
        http_response_code(400);
        echo "Invalid rating submission.";
        exit;
    }

    // Ensure tenant exists
    $st = $pdo->prepare("SELECT id FROM tenants WHERE id = ?");
    $st->execute([$tenant_id]);
    if (!$st->fetch()) {
        http_response_code(404);
        echo "Tenant not found.";
        exit;
    }

    // Insert rating into `ratings` table
    $ins = $pdo->prepare("
      INSERT INTO ratings (tenant_id, score, author_name, author_email, comment, created_at)
      VALUES (:tenant_id, :score, :author_name, :author_email, :comment, NOW())
    ");
    $ins->execute([
        ':tenant_id'    => $tenant_id,
        ':score'        => $score,
        ':author_name'  => $author_name !== ''  ? $author_name  : null,
        ':author_email' => $author_email !== '' ? $author_email : null,
        ':comment'      => $comment !== ''      ? $comment      : null,
    ]);

    // Recalculate tenant rating_avg + rating_count on `tenants` table
    $agg = $pdo->prepare("
      SELECT AVG(score) AS avg_score, COUNT(*) AS cnt
      FROM ratings
      WHERE tenant_id = ?
    ");
    $agg->execute([$tenant_id]);
    $row = $agg->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $avg = $row['avg_score'] !== null ? round((float)$row['avg_score'], 2) : null;
        $cnt = (int)($row['cnt'] ?? 0);

        $upd = $pdo->prepare("
          UPDATE tenants
          SET rating_avg = :avg, rating_count = :cnt
          WHERE id = :id
        ");
        $upd->execute([
            ':avg' => $avg,
            ':cnt' => $cnt,
            ':id'  => $tenant_id,
        ]);
    }

    // Redirect to the tenant profile (this was working; the 500 was in profile.php)
    header("Location: /profile.php?id=" . $tenant_id);
    exit;
}

// ---------- GET: show form ----------
$tenant_id = isset($_GET['tenant_id']) ? (int)$_GET['tenant_id'] : 0;

// Optional: load tenant for name display
$tenant = null;
if ($tenant_id > 0) {
    $st = $pdo->prepare("SELECT business_name FROM tenants WHERE id = ?");
    $st->execute([$tenant_id]);
    $tenant = $st->fetch(PDO::FETCH_ASSOC);
}

$title = "Rate Tenant — Aban Suites";
include __DIR__ . '/partials_header.php';
?>

<div class="container card">
  <?php if ($tenant_id <= 0 || !$tenant): ?>
    <p>Missing or invalid tenant. <a href="/tenants.php">Back to tenants</a>.</p>
  <?php else: ?>
    <h2>Rate <?= \App\Util::e($tenant['business_name']) ?></h2>
    <p class="muted">Share your experience to help others choose the right professional.</p>

    <form method="post" action="/rate_tenant.php">
      <?= \App\CSRF::field(); ?>
      <input type="hidden" name="tenant_id" value="<?= $tenant_id ?>">

      <div class="form-row two">
        <label>
          Your name (optional)
          <input type="text" name="author_name">
        </label>
        <label>
          Email (optional)
          <input type="email" name="author_email">
        </label>
      </div>

      <div class="form-row">
        <label>
          Rating
          <select name="score" required>
            <option value="">Select…</option>
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <option value="<?= $i ?>"><?= $i ?> ★</option>
            <?php endfor; ?>
          </select>
        </label>
      </div>

      <div class="form-row">
        <label>
          Comment (optional)
          <textarea name="comment" rows="4" placeholder="What did you think?"></textarea>
        </label>
      </div>

      <div class="form-row">
        <button type="submit" class="btn primary sm">Submit rating</button>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials_footer.php'; ?>
