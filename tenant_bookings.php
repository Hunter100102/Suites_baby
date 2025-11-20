<?php
use App\DB; use App\Util;
session_start();
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'tenant') { header("Location: /tenant_login.php"); exit; }
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$pdo = DB::pdo();
$uid = (int)($_SESSION['user']['id'] ?? 0);
$tenStmt = $pdo->prepare("SELECT t.* FROM tenants t JOIN users u ON u.id=t.user_id WHERE u.id=?");
$tenStmt->execute([$uid]); $ten = $tenStmt->fetch();
if (!$ten) { http_response_code(403); echo "No tenant profile."; exit; }
$title = "My Bookings — Aban Suites";
include __DIR__ . "/partials_header.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
  if (hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $action = $_POST['action'];
    $booking_id = (int)($_POST['booking_id'] ?? 0);
    if (in_array($action, ['pending','confirmed','cancelled','completed'], true) && $booking_id > 0) {
      $up = $pdo->prepare("UPDATE bookings SET status=? WHERE id=? AND tenant_id=?");
      $up->execute([$action, $booking_id, $ten['id']]);
      header("Location: /tenant_bookings.php"); exit;
    }
  }
}
$rows = $pdo->prepare("SELECT * FROM bookings WHERE tenant_id=? ORDER BY created_at DESC");
$rows->execute([$ten['id']]); $data = $rows->fetchAll();
?>
<div class="container card">
  <h2>My Bookings</h2>
  <table class="table">
    <thead>
      <tr><th>Requested</th><th>Customer</th><th>Contact</th><th>Preferred</th><th>Service</th><th>Message</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $b): ?>
      <tr>
        <td><?= Util::e($b['created_at']) ?></td>
        <td><?= Util::e($b['customer_name']) ?></td>
        <td><?= Util::e(trim(($b['customer_email'] ? $b['customer_email'].' · ' : '').($b['customer_phone'] ?? ''))) ?></td>
        <td><?= Util::e(trim(($b['requested_date'] ?? '').' '.($b['requested_time'] ?? ''))) ?></td>
        <td><?= Util::e($b['service_note'] ?? '') ?></td>
        <td><?= nl2br(Util::e($b['message'] ?? '')) ?></td>
        <td><span class="badge"><?= Util::e($b['status']) ?></span></td>
        <td>
          <form method="post" style="display:flex; gap:6px; align-items:center;">
            <input type="hidden" name="csrf" value="<?= Util::e($_SESSION['csrf']) ?>">
            <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
            <select name="action" class="input">
              <option value="">Change status…</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirm</option>
              <option value="cancelled">Cancel</option>
              <option value="completed">Completed</option>
            </select>
            <button class="btn">Update</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . "/partials_footer.php"; ?>
