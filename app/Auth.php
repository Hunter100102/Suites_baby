<?php
namespace App;
class Auth {
    public static function start(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.cookie_httponly', '1');
            session_start();
        }
    }
    public static function user() {
        return $_SESSION['user'] ?? null;
    }
    public static function requireRole(string $role): void {
        self::start();
        $u = self::user();
        if (!$u || $u['role'] !== $role) {
            header("Location: /login.php"); exit;
        }
    }
    public static function login(array $user): void {
        self::start();
        $_SESSION['user'] = $user;
    }
    public static function logout(): void {
        self::start();
        $_SESSION = [];
        session_destroy();
    }
}
