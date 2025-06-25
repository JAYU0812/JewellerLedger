<?php
$jeweller_id = $_GET['jeweller_id'] ?? null;
$customer_id = $_GET['customer_id'] ?? null;

if (!$jeweller_id || !$customer_id) {
    die("Jeweller or customer not specified.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeweller Ledger - Select Transaction Type</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Custom styles for body and font-family */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 text-gray-800 relative">

    <!-- Navigation Buttons Container -->
    <div class="absolute top-4 left-4 flex gap-2">
        <!-- Back to Dashboard Button -->
        <a href="dashboard.php?jeweller_id=<?= htmlspecialchars($jeweller_id) ?>"
           class="inline-flex items-center p-3 rounded-full bg-white shadow-lg text-gray-700 hover:bg-gray-100 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
           title="Back to Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="sr-only">Back to Dashboard</span>
        </a>

        <!-- Back Button (to Select Customer) -->
        <a href="select_customer.php?jeweller_id=<?= htmlspecialchars($jeweller_id) ?>"
           class="inline-flex items-center p-3 rounded-full bg-white shadow-lg text-gray-700 hover:bg-gray-100 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
           title="Back to Select Customer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            <span class="sr-only">Back to Select Customer</span>
        </a>
    </div>

    <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl text-center max-w-md w-full transform transition-all duration-300 hover:scale-105">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-10">
            Select Transaction Type
        </h2>

        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <a href="transaction_category.php?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer_id ?>&type=jama"
               class="block w-full sm:w-auto">
                <button class="w-full inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold text-lg rounded-full shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Purchase (Jama)
                </button>
            </a>

            <a href="transaction_category.php?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer_id ?>&type=udhar"
               class="block w-full sm:w-auto">
                <button class="w-full inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold text-lg rounded-full shadow-lg hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Sale (Udhar)
                </button>
            </a>
        </div>
    </div>

</body>
</html>
