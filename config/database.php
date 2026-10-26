<?php
/**
 * Database Configuration
 * SineSine - Movie Rating and Review Website
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sinesine_db');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Friendly error message without exposing credentials
            die("<div style='font-family:sans-serif;padding:20px;background:#1a1d24;color:#f8d7da;border:1px solid #f5c6cb;border-radius:6px;max-width:600px;margin:40px auto;'>
                <h3 style='color:#e50914;margin-top:0;'>Database Connection Error</h3>
                <p>Could not connect to the database <strong>" . htmlspecialchars(DB_NAME) . "</strong>.</p>
                <p><small>Please make sure MySQL is started in XAMPP and the database has been imported.</small></p>
                <p><small>Details: " . htmlspecialchars($e->getMessage()) . "</small></p>
            </div>");
        }
    }
    return $pdo;
}

