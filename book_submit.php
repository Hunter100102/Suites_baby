<?php
// Handles appointment booking form POST

use App\Env;
use App\DB;
use App\CSRF;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/CSRF.php';
require_once __DIR__ . '/../app/Util.php';

App\Env::load(__DIR__ . '/../.env');
\App\Auth::start();

// CSRF protection
\App\CSRF::check();

$tenant_id       = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
$customer_name   = trim($_POST['customer_name']   ?? '');
$customer_email  = trim($_POST['customer_email']  ?? '');
$customer_phone  = trim($_POST['customer_phone']  ?? '');
$requested_date  = trim($_POST['requested_date']  ?? '');
$requested_time  = trim($_POST['requested_time']  ?? '');
$service_note    = trim($_POST['service_note']    ?? '');
$message         = trim($_POST['message']         ?? '');

if ($tenant_id <= 0 || $customer_name === '') {
    http_response_code(400);
    echo "Missing required fields.";
    exit;
}

$pdo = DB::pdo();

// Optional: verify tenant exists
$check = $pdo->prepare("SELECT id FROM tenants WHERE id = ?");
$check->execute([$tenant_id]);
if (!$check->fetch()) {
    http_response_code(404);
    echo "Tenant not found.";
    exit;
}

// Normalize empty date/time to NULL
$requested_date = $requested_date !== '' ? $requested_date : null;
$requested_time = $requested_time !== '' ? $requested_time : null;

$sql = "
  INSERT INTO bookings (
    tenant_id,
    customer_name,
    customer_email,
    customer_phone,
    requested_date,
    requested_time,
    service_note,
    message,
    status,
    created_at
  ) VALUES (
    :tenant_id,
    :customer_name,
    :customer_email,
    :customer_phone,
    :requested_date,
    :requested_time,
    :service_note,
    :message,
    'new',
    NOW()
  )
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':tenant_id'      => $tenant_id,
    ':customer_name'  => $customer_name,
    ':customer_email' => $customer_email ?: null,
    ':customer_phone' => $customer_phone ?: null,
    ':requested_date' => $requested_date,
    ':requested_time' => $requested_time,
    ':service_note'   => $service_note ?: null,
    ':message'        => $message ?: null,
]);

// Redirect back to profile
header("Location: /profile.php?id=" . $tenant_id);
exit;
