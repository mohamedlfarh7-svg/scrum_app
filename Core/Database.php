<?php

class Database {
    private static $conn = null;

    public static function connect() {
        if (self::$conn === null) {
            $config = require __DIR__ . '/../config/config.php';
            self::$conn = new PDO(
                "mysql:host={$config['db_host']};dbname={$config['db_name']}",
                $config['db_user'],
                $config['db_pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$conn;
    }
}
?>