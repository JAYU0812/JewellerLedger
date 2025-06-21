<?php
$jeweller_id = $_GET['jeweller_id'] ?? null;
$customer_id = $_GET['customer_id'] ?? null;
$type = $_GET['type'] ?? null;

if (!$jeweller_id || !$customer_id || !$type) {
    die("Invalid transaction request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jeweller Ledger - Select Metal Category</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* Custom styles for body and font-family */
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 text-gray-800">

  <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl text-center max-w-md w-full transform transition-all duration-300 hover:scale-105">
    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-10">
      Choose Metal Category
    </h2>

    <div class="flex flex-col sm:flex-row gap-6 justify-center">
      <a href="transaction_form.php?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer_id ?>&type=<?= $type ?>&category=gold"
         class="block w-full sm:w-auto">
        <button class="w-full inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white font-semibold text-lg rounded-full shadow-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.503 4.637a1 1 0 00.95.69h4.873c.969 0 1.371 1.24.588 1.81l-3.93 2.988a1 1 0 00-.364 1.118l1.502 4.637c.3.921-.755 1.688-1.539 1.118l-3.93-2.988a1 1 0 00-1.176 0l-3.93 2.988c-.784.57-1.838-.197-1.539-1.118l1.502-4.637a1 1 0 00-.364-1.118L2.055 10.064c-.783-.57-.381-1.81.588-1.81h4.873a1 1 0 00.95-.69l1.502-4.637z" />
          </svg>
          Gold
        </button>
      </a>

      <a href="transaction_form.php?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer_id ?>&type=<?= $type ?>&category=silver"
         class="block w-full sm:w-auto">
        <button class="w-full inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-gray-400 to-gray-500 text-white font-semibold text-lg rounded-full shadow-lg hover:from-gray-500 hover:to-gray-600 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 0a9 9 0 01-9-9m9 9a9 9 0 00-9 9m9-9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9h.02" />
          </svg>
          Silver
        </button>
      </a>
    </div>
  </div>

</body>
</html>