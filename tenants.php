<?php
use App\DB;
use App\Util;

$title = "Tenants — Aban Suites";
include __DIR__ . "/partials_header.php";

$pdo = DB::pdo();

// Get tenants + user contact + rating
$sql = "
  SELECT 
    t.*,
    u.email,
    u.phone,
    COALESCE(t.rating_avg, t.rating) AS display_rating,
    t.rating_count
  FROM tenants t
  LEFT JOIN users u ON t.user_id = u.id
  ORDER BY t.business_name
";
$stmt = $pdo->query($sql);
$tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container card">
  <div class="search">
    <input id="tenant-search" type="text" placeholder="Search tenants, services..." />
  </div>

  <div id="tenant-list">
    <?php foreach ($tenants as $t): 
      $ratingValue = $t['display_rating'] !== null ? (float)$t['display_rating'] : null;
      $ratingCount = isset($t['rating_count']) ? (int)$t['rating_count'] : 0;

      // Fetch up to 6 example photos for this tenant
      $photosStmt = $pdo->prepare("
        SELECT *
        FROM tenant_photos
        WHERE tenant_id = ?
        ORDER BY id DESC
        LIMIT 6
      ");
      $photosStmt->execute([$t['id']]);
      $photos = $photosStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
      <div class="tenant" id="t<?= (int)$t['id'] ?>">
        <img
          src="<?= Util::e($t['avatar'] ?: '/assets/img/avatar-placeholder.jpg') ?>"
          alt="<?= Util::e($t['business_name']) ?>"
        >
        <div>
          <strong><?= Util::e($t['business_name']) ?></strong>

          <?php if ($ratingValue !== null): ?>
            <div class="badge rating-badge">
              ★ <?= number_format($ratingValue, 1) ?>
              <?php if ($ratingCount > 0): ?>
                (<?= $ratingCount ?>)
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($t['room_number'])): ?>
            <div class="badge">
              Room <?= Util::e((string)$t['room_number']) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($t['services'])): ?>
            <p class="tenant-services">
              Services: <?= nl2br(Util::e($t['services'])) ?>
            </p>
          <?php endif; ?>

          <?php if ($photos): ?>
            <details class="tenant-examples">
              <summary>View examples</summary>
              <div class="tenant-photos">
                <?php foreach ($photos as $ph): ?>
                  <img
                    src="<?= Util::e($ph['file_path']) ?>"
                    alt="<?= Util::e($ph['caption'] ?? '') ?>"
                  >
                <?php endforeach; ?>
              </div>
            </details>
          <?php endif; ?>
        </div>
      </div>

      <div class="tenant-actions">
        <a class="btn primary sm" href="/book.php?tenant_id=<?= (int)$t['id'] ?>">Book appointment</a>
        <a class="btn sm" href="/profile.php?id=<?= (int)$t['id'] ?>">View profile</a>
        <a class="btn sm" href="/rate_tenant.php?tenant_id=<?= (int)$t['id'] ?>">Rate this tenant</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
// Simple search filter by tenant name / services
document.getElementById('tenant-search')?.addEventListener('input', function () {
  const q = this.value.toLowerCase();
  const cards = document.querySelectorAll('#tenant-list .tenant');

  cards.forEach(card => {
    const text = card.innerText.toLowerCase();
    const visible = text.indexOf(q) !== -1;
    card.style.display = visible ? '' : 'none';

    // Hide/show button row under card
    const btnRow = card.nextElementSibling;
    if (btnRow && btnRow.classList.contains('tenant-actions')) {
      btnRow.style.display = visible ? '' : 'none';
    }
  });
});
</script>

<?php include __DIR__ . "/partials_footer.php"; ?>
