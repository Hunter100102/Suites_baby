<?php
use App\DB; use App\CSRF; use App\Util; use App\Auth;
$title="Login — Aban Suites"; include __DIR__ . "/partials_header.php";
$pdo = DB::pdo();
$err = "";
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (Util::hp()) die("Bot detected.");
  CSRF::check();
  $email = filter_var(Util::post('email'), FILTER_VALIDATE_EMAIL);
  $pass = Util::post('password');
  if ($email && $pass) {
    $st = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $st->execute([$email]);
    $u = $st->fetch();
    if ($u && password_verify($pass, $u['password_hash'])) {
      Auth::login($u);
      if ($u['role']==='admin') { header("Location: /admin.php"); exit; }
      header("Location: /tenant_dashboard.php"); exit;
    } else $err = "Invalid credentials.";
  } else $err = "Enter email and password.";
}
?>
<div class="container card">
  <h2>Login</h2>
  <?php if ($err): ?><p class="notice"><?= App\Util::e($err) ?></p><?php endif; ?>
  <form method="POST">
    <input type="hidden" name="confirm_bot" value="">
    <?= App\CSRF::field() ?>
    <div class="form-row">
      <div><label>Email</label><input type="email" name="email" required></div>
      <div><label>Password</label><input type="password" name="password" required></div>
    </div>
    <button class="btn cta" type="submit">Sign in</button>
  </form>
</div>
<?php include __DIR__ . "/partials_footer.php"; ?>
