<?php
use App\Auth;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Env.php';
require_once __DIR__ . '/../app/Auth.php';
App\Env::load(__DIR__.'/../.env');
Auth::logout();
header("Location: /");
