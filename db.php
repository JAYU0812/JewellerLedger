<?php
$host = 'sql213.infinityfree.com';
$db   = 'if0_39278499_ledger';
$user = 'if0_39278499';
$pass = 'QD2ZI12fLPO';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
