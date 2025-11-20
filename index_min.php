<?php
declare(strict_types=1);
ini_set('display_errors', '1'); error_reporting(E_ALL);

require __DIR__ . '/partials_header.php';

?>
<div class="container card" style="margin:40px auto;max-width:680px">
  <h2>Minimal page loaded ✅</h2>
  <p>If you can see this, PHP + includes are fine. Next we’ll test the API call the layout uses.</p>
  <p><a href="/api/rooms.php" target="_blank">Test /api/rooms.php</a></p>
</div>
<?php include __DIR__ . '/partials_footer.php'; ?>
