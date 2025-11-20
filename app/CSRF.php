<?php
namespace App;
class CSRF {
    public static function token(): string {
        Auth::start();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return hash_hmac('sha256', $_SESSION['csrf'], Env::get('CSRF_SECRET','secret'));
    }
    public static function field(): string {
        return '<input type="hidden" name="csrf" value="'.htmlspecialchars(self::token(),ENT_QUOTES).'">';
    }
    public static function check(): void {
        Env::get('CSRF_SECRET') ?: die("CSRF not configured.");
        $ok = isset($_POST['csrf']) && hash_equals(self::token(), $_POST['csrf']);
        if (!$ok) { http_response_code(400); die("Invalid CSRF token"); }
    }
}
