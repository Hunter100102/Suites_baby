<?php
namespace App;

use PDO;
use PDOException;

class DB {
    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (self::$pdo === null) {
            $host = Env::get('DB_HOST', '127.0.0.1');
            $port = (int)Env::get('DB_PORT', 3306);
            $name = Env::get('DB_NAME');
            $user = Env::get('DB_USER');
            $pass = Env::get('DB_PASS');

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Minimal, user-safe message (details go to error_log)
                error_log('DB connect failed: ' . $e->getMessage());
                http_response_code(500);
                die('Database connection failed. Please try again later.');
            }
        }
        return self::$pdo;
    }
}
