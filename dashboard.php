<?php
require 'db.php';

$jeweller_id = $_GET['jeweller_id'] ?? null;
if (!$jeweller_id) {
    die("Access denied. Jeweller ID missing.");
}

// ✅ Fetch jeweller name
$stmt = $pdo->prepare("SELECT name FROM jewellers WHERE id = ?");
$stmt->execute([$jeweller_id]);
$jeweller = $stmt->fetch();
$jeweller_name = $jeweller ? $jeweller['name'] : "Jeweller";

// Fetch customers
$stmt = $pdo->prepare("SELECT * FROM customers WHERE jeweller_id = ?");
$stmt->execute([$jeweller_id]);
$customers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Jeweller Ledger - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="min-h-screen p-4 bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 text-gray-800">

  <div class="container mx-auto px-4 py-8">

    <!-- ✅ Logout Button -->
    <div class="text-right mb-4">
      <a href="logout.php"
         class="inline-block px-4 py-2 bg-red-500 text-white font-semibold rounded-full hover:bg-red-600 transition duration-200 shadow">
        🚪 Logout
      </a>
    </div>

    <!-- Welcome -->
    <h2 class="text-4xl sm:text-5xl font-extrabold text-center mb-8">
      Welcome, <span class="text-blue-600"><?= htmlspecialchars($jeweller_name) ?></span>
    </h2>

    <!-- Start New Transaction -->
    <div class="mb-12 text-center">
      <a href="select_customer.php?jeweller_id=<?= $jeweller_id ?>"
         class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold text-lg rounded-full shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Start New Transaction
      </a>
    </div>

    <!-- Customer List -->
    <h3 class="text-3xl sm:text-4xl font-bold text-center mb-6">Your Customers:</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($customers as $customer): ?>
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col items-center text-center">
          <strong class="text-2xl font-semibold mb-4 text-gray-900"><?= htmlspecialchars($customer['name']) ?></strong>
          
          <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
            <a href="transaction_type.php?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer['id'] ?>"
               class="flex-1 w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium text-base rounded-full shadow-md hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
              ➕ Add Transaction
            </a>

            <a href="view_ledger.php?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer['id'] ?>"
               class="flex-1 w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium text-base rounded-full shadow-md hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
              📄 View Ledger
            </a>

            <form action="delete_customer.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');">
              <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
              <input type="hidden" name="jeweller_id" value="<?= $jeweller_id ?>">
              <button type="submit"
                      class="flex-1 w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-medium text-base rounded-full shadow-md hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-300">
                🗑️ Delete
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</body>
</html>
