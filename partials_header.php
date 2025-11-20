<?php
declare(strict_types=1);

use App\Env;
use App\Auth;

$root = dirname(__DIR__);

// Load Composer if present (PHPMailer & class autoload)
$autoload = $root . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

require_once $root . '/app/Env.php';
require_once $root . '/app/DB.php';
require_once $root . '/app/Auth.php';
require_once $root . '/app/CSRF.php';
require_once $root . '/app/Util.php';

// Load env from one level above public_html
App\Env::load($root . '/.env');

App\Auth::start();

$title = $title ?? (App\Env::get('APP_NAME', 'Aban Suites'));

// Security headers (fine to keep)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 0'); // modern browsers rely on CSP
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Basic SEO -->
  <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
  <meta name="description" content="Private suites for beauty &amp; wellness professionals. Tour, pick a room, and join our community." />
  <meta name="theme-color" content="#0d6efd" />

  <!-- Styles & Media -->
  <link rel="stylesheet" href="/assets/css/styles.css" />
  <link rel="preload" as="video" href="/assets/video/hero.mp4" />
</head>
<body>
<header>
  <div class="nav">
    <div class="brand">
      <a href="/" class="logo-link">
        <img src="/assets/img/logo.png" alt="Aban Suites Logo" class="logo-img" />
        <span class="brand-text">Aban Suites</span>
      </a>
    </div>

    <!-- ✅ Hamburger toggle for mobile -->
    <button class="nav-toggle" type="button" aria-label="Toggle navigation">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <nav class="menu">
      <a href="/" class="<?= (($_SERVER['SCRIPT_NAME'] ?? '') === '/index.php') ? 'active' : '' ?>">Home</a>
      <a href="/tenants.php" class="<?= (($_SERVER['SCRIPT_NAME'] ?? '') === '/tenants.php') ? 'active' : '' ?>">Tenants</a>
      <a href="/login.php">Tenant Login</a>
      <a href="/admin.php">Admin</a>
    </nav>
  </div>
</header>
