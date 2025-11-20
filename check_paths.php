<?php
$root = dirname(__DIR__);
$checks = [
  $root . '/app/Env.php',
  $root . '/app/DB.php',
  $root . '/vendor/autoload.php',
  $root . '/.env',
];

echo "<pre>";
foreach ($checks as $p) {
  echo (file_exists($p) ? "OK   " : "MISS ") . $p . "\n";
}
echo "</pre>";
