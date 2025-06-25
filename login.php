<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $passcode = $_POST['passcode'];

    $stmt = $pdo->prepare("SELECT * FROM jewellers WHERE name = ?");
    $stmt->execute([$name]);
    $user = $stmt->fetch();

    if ($user && password_verify($passcode, $user['passcode_hash'])) {
        header("Location: dashboard.php?jeweller_id=" . $user['id']);
        exit;
    } else {
        echo "Invalid credentials.";
    }
}
?>