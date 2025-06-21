<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? null;
    $jeweller_id = $_POST['jeweller_id'] ?? null;

    if ($customer_id && $jeweller_id) {
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ? AND jeweller_id = ?");
        $stmt->execute([$customer_id, $jeweller_id]);
    }
    
    header("Location: dashboard.php?jeweller_id=" . urlencode($jeweller_id));
    exit;
} else {
    echo "Invalid request.";
}
