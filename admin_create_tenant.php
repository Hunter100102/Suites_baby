<?php
use App\DB; use App\Auth; use App\CSRF; use App\Util;
use PHPMailer\PHPMailer\PHPMailer; use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/CSRF.php';
require_once __DIR__ . '/../app/Util.php';

App\Env::load(__DIR__.'/../.env');
Auth::start();
$u = Auth::user();
if (!$u || $u['role']!=='admin') { header('Location:/login.php'); exit; }
$pdo = DB::pdo();

$pdo->exec("ALTER TABLE tenants ADD COLUMN IF NOT EXISTS room_number INT NULL");
$pdo->exec("CREATE TABLE IF NOT EXISTS onboarding_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id INT NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!CSRF::check($_POST['csrf'] ?? '')) { http_response_code(400); echo "Bad CSRF"; exit; }
  $email = trim($_POST['email'] ?? ''); $phone = trim($_POST['phone'] ?? '');
  $biz = trim($_POST['business_name'] ?? ''); $services = trim($_POST['services'] ?? '');
  $room = intval($_POST['room_number'] ?? 0);
  if (!$email || !$biz || $room<=0 || $room>20) { echo "Missing or invalid fields"; exit; }

  // Create or find user
  $st = $pdo->prepare("SELECT id FROM users WHERE email=?");
  $st->execute([$email]); $user_id = $st->fetchColumn();
  if (!$user_id) {
    $pwd = bin2hex(random_bytes(4));
    $insU = $pdo->prepare("INSERT INTO users (email, phone, role, password_hash) VALUES (?,?, 'tenant', ?)");
    $insU->execute([$email, $phone, password_hash($pwd, PASSWORD_DEFAULT)]);
    $user_id = (int)$pdo->lastInsertId();
  }

  // Create tenant and assign room
  $insT = $pdo->prepare("INSERT INTO tenants (user_id, business_name, services, room_number) VALUES (?,?,?,?)");
  $insT->execute([$user_id, $biz, $services, $room]);
  $tenant_id = (int)$pdo->lastInsertId();
  $pdo->prepare("UPDATE rooms SET status='occupied' WHERE room_number=?")->execute([$room]);

  // Onboarding token + email
  $token = bin2hex(random_bytes(16));
  $pdo->prepare("INSERT INTO onboarding_tokens (tenant_id, token) VALUES (?,?)")->execute([$tenant_id, $token]);

  $from  = $_ENV['SMTP_FROM'] ?? $_ENV['SMTP_USER'] ?? 'no-reply@abansuites.com';
  $appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
  if (!$appUrl && !empty($_SERVER['HTTP_HOST'])) { $appUrl = 'https://' . $_SERVER['HTTP_HOST']; }
  $link = $appUrl . "/doorloop_onboard.php?token=" . urlencode($token);

  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
    $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Username   = $_ENV['SMTP_USER'] ?? '';
    $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
    $mail->setFrom($from, 'Aban Suites');
    $mail->addAddress($email);
    $mail->Subject = "Welcome to Aban Suites — Complete Your Profile";
    $mail->isHTML(true);
    $mail->Body = "Hi,<br>You've been approved and assigned to Room {$room} at Aban Suites.<br>
                   Please complete your profile (DoorLoop-ready) here: <a href='{$link}'>{$link}</a><br><br>— Aban Suites";
    $mail->AltBody = "You've been approved for Room {$room}. Complete your profile: {$link}";
    $mail->send();
  } catch (Exception $e) { /* log if needed */ }

  header("Location: /admin.php?created=1#tenant-".$tenant_id); exit;
}
?>
<?php $title = "Create Tenant"; include __DIR__ . "/partials_header.php"; ?>
<div class="container card">
  <h2>Create Tenant & Assign Room</h2>
  <form method="post" action="/admin_create_tenant.php">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(App\CSRF::token()) ?>">
    <div class="form-row two">
      <div><label>Business Name</label><input name="business_name" required></div>
      <div><label>Services</label><input name="services"></div>
    </div>
    <div class="form-row two">
      <div><label>Contact Email</label><input type="email" name="email" required></div>
      <div><label>Phone</label><input name="phone"></div>
    </div>
    <div class="form-row two">
      <div><label>Room #</label><input type="number" name="room_number" min="1" max="20" required></div>
      <div style="align-self:end;"><button class="btn" type="submit">Create & Assign</button></div>
    </div>
  </form>
</div>
<?php include __DIR__ . "/partials_footer.php"; ?>
