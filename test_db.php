<?php
declare(strict_types=1);

// Show errors for this test only
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Minimal inline env loader (no Composer needed)
function load_env(string $path): void {
    if (!is_file($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        $v = trim($v, "\"'");
        $_ENV[$k] = $v;
        $_SERVER[$k] = $v;
        putenv("$k=$v");
    }
}

$root = dirname(__DIR__);
load_env($root . '/.env');

// Use 127.0.0.1 on Hostinger for MySQL host
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = (int)($_ENV['DB_PORT'] ?? 3306);
$name = $_ENV['DB_NAME'] ?? '';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';

echo "<pre>Env loaded:\n";
printf("DB_HOST=%s\nDB_NAME=%s\nDB_USER=%s\n\n", $host, $name, $user);

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $row = $pdo->query("SELECT COUNT(*) AS c FROM rooms")->fetch();
    echo "DB connection OK ✅\n";
    echo "rooms count: " . ($row['c'] ?? 'n/a') . "\n";
} catch (Throwable $e) {
    echo "DB connection FAILED ❌\n";
    echo $e::class . ": " . $e->getMessage() . "\n";
}
echo "</pre>";
