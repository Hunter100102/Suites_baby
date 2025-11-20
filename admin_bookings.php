<?php
use App\DB; use App\Util;
session_start();
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') { header("Location: /admin_login.php"); exit; }
$title = "All Bookings — Aban Suites";
$pdo = DB::pdo();
$rows = $pdo->query("SELECT b.*, t.business_name FROM bookings b JOIN tenants t ON t.id=b.tenant_id ORDER BY b.created_at DESC")->fetchAll();
include __DIR__ . "/partials_header.php";
?>
<div class="container card">
  <h2>All Bookings</h2>
  <table class="table">
    <thead>
      <tr><th>When</th><th>Tenant</th><th>Customer</th><th>Contact</th><th>Preferred</th><th>Service</th><th>Status</th></tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $b): ?>
      <tr>
        <td><?= Util::e($b['created_at']) ?></td>
        <td><?= Util::e($b['business_name']) ?></td>
        <td><?= Util::e($b['customer_name']) ?></td>
        <td><?= Util::e(trim(($b['customer_email'] ? $b['customer_email'].' · ' : '').($b['customer_phone'] ?? ''))) ?></td>
        <td><?= Util::e(trim(($b['requested_date'] ?? '').' '.($b['requested_time'] ?? ''))) ?></td>
        <td><?= Util::e($b['service_note'] ?? '') ?></td>
        <td><span class="badge"><?= Util::e($b['status']) ?></span></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . "/partials_footer.php"; ?>
