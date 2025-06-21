<?php
require 'db.php';

$jeweller_id = $_GET['jeweller_id'] ?? null;
if (!$jeweller_id) {
    die("Jeweller not specified.");
}

// Handle new customer creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name']);
    if ($customer_name !== '') {
        $stmt = $pdo->prepare("INSERT INTO customers (jeweller_id, name) VALUES (?, ?)");
        $stmt->execute([$jeweller_id, $customer_name]);
        $new_id = $pdo->lastInsertId();
        header("Location: transaction_type.php?jeweller_id=$jeweller_id&customer_id=$new_id");
        exit;
    }
}

// Fetch all existing customers
$stmt = $pdo->prepare("SELECT * FROM customers WHERE jeweller_id = ?");
$stmt->execute([$jeweller_id]);
$customers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jeweller Ledger - Select Customer</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Font: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* Custom styles for body and font-family */
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 text-gray-800">

  <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl text-center max-w-lg w-full transform transition-all duration-300 hover:scale-105">
    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-8">
      Start Transaction
    </h2>

    <div class="mb-10 p-6 bg-gray-50 rounded-lg shadow-inner">
      <h3 class="text-xl sm:text-2xl font-bold text-gray-700 mb-6">Create New Customer</h3>
      <form method="POST" class="space-y-6">
        <div>
          <input
            type="text"
            name="customer_name"
            placeholder="Enter customer name"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-lg placeholder-gray-500"
          >
        </div>
        <button
          type="submit"
          class="w-full px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold text-lg rounded-full shadow-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-purple-300"
        >
          Create & Continue
        </button>
      </form>
    </div>

    <h3 class="text-xl sm:text-2xl font-bold text-gray-700 mb-6">Or Select Existing Customer</h3>
    
    <?php if (empty($customers)): ?>
        <p class="text-gray-600 italic">No existing customers yet. Create one above!</p>
    <?php else: ?>
        <ul class="space-y-4 max-h-64 overflow-y-auto custom-scrollbar">
            <?php foreach ($customers as $customer): ?>
            <li>
                <a href="transaction_type.php?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer['id'] ?>"
                   class="flex items-center justify-between px-6 py-4 bg-blue-50 hover:bg-blue-100 rounded-lg shadow-md transition duration-200 text-lg font-medium text-blue-700 hover:text-blue-800 transform hover:scale-105">
                    <span><?= htmlspecialchars($customer['name']) ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
  </div>

</body>
</html>