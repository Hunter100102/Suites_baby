<?php
namespace App;
class Util {
    public static function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES); }
    public static function post(string $k, $default=''){ return trim($_POST[$k] ?? $default); }
    public static function hp(): bool { return !empty($_POST['confirm_bot'] ?? ''); }
}
