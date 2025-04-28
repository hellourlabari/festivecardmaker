<?php
$host = 'sql311.infinityfree.com';
$dbname = 'if0_36536968_wp444';
$username = 'if0_36536968';
$password = 'dOzbV1AKHuucUr';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 