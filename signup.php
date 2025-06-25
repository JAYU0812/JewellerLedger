<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/plain');
header('Access-Control-Allow-Origin: *');
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $passcode = $_POST['passcode'];

    if ($name && $passcode) {
        $hash = password_hash($passcode, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO jewellers (name, passcode_hash) VALUES (?, ?)");

        try {
            $stmt->execute([$name, $hash]);
            $jeweller_id = $pdo->lastInsertId();
            echo "success|$jeweller_id";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "error|Jeweller name already exists.";
            } else {
                echo "error|Database error: " . $e->getMessage();
            }
        }
    } else {
        echo "error|Please fill all fields.";
    }
} else {
    echo "error|Invalid request method.";
}
?>