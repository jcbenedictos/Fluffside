<?php
$host = 'localhost';
$dbname = 'fluffside_db';
$username = 'root';
$password = '';

// Set timezone for consistency
date_default_timezone_set('America/Chicago'); // Change to your timezone

try {
    // Add timezone to DSN
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set session timezone to match PHP timezone
    $pdo->exec("SET SESSION sql_mode='STRICT_TRANS_TABLES'");
    $pdo->exec("SET time_zone = '+00:00'"); // UTC; will be converted by PHP
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!-- for clean comments -->