<?php
use App\CSRF;
use App\Util;

$title = "Book Appointment — Aban Suites";
include __DIR__ . "/partials_header.php";

// Tenant id from query
$tenant_id = isset($_GET['tenant_id']) ? (int)$_GET['tenant_id'] : 0;
?>
<div class="container card">
  <h2>Book a Visit</h2>
  <p class="muted">Send a quick request and the tenant will follow up to confirm your time.</p>

  <?php if ($tenant_id <= 0): ?>
    <p>Missing tenant information. <a href="/tenants.php">Back to tenants</a>.</p>
  <?php else: ?>
    <form method="post" action="/book_submit.php" class="form-grid">
      <?= \App\CSRF::field(); ?>
      <input type="hidden" name="tenant_id" value="<?= $tenant_id ?>">

      <div class="form-row two">
        <label>
          Full name
          <input type="text" name="customer_name" required>
        </label>

        <label>
          Phone
          <input type="text" name="customer_phone">
        </label>
      </div>

      <div class="form-row two">
        <label>
          Email
          <input type="email" name="customer_email">
        </label>

        <label>
          Preferred date
          <input type="date" name="requested_date">
        </label>
      </div>

      <div class="form-row two">
        <label>
          Preferred time
          <input type="time" name="requested_time">
        </label>

        <label>
          Service of interest
          <input type="text" name="service_note" placeholder="e.g. hair color, braids, nails">
        </label>
      </div>

      <div class="form-row">
        <label>
          Message (optional)
          <textarea name="message" rows="4" placeholder="Anything the tenant should know?"></textarea>
        </label>
      </div>

      <div class="form-row">
        <button class="btn primary sm" type="submit">Send request</button>
      </div>
    </form>

    <p class="muted" style="margin-top: 8px;">
      You’ll receive a confirmation or follow-up by email or phone.
    </p>
  <?php endif; ?>
</div>

<?php include __DIR__ . "/partials_footer.php"; ?>
