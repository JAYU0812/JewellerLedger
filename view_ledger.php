<?php
require 'db.php';

$jeweller_id = $_GET['jeweller_id'] ?? null;
$customer_id = $_GET['customer_id'] ?? null;
$category = $_GET['category'] ?? null; // Changed to null

$from_date = $_GET['from'] ?? null;
$to_date = $_GET['to'] ?? null;

if (!$jeweller_id || !$customer_id) {
    die("Missing jeweller or customer.");
}

// Fetch customer name
$stmt = $pdo->prepare("SELECT name FROM customers WHERE id = ? AND jeweller_id = ?");
$stmt->execute([$customer_id, $jeweller_id]);
$customer = $stmt->fetch();
if (!$customer) {
    die("Invalid customer.");
}
$customer_name = $customer['name'];

// Initialize transactions, jama, udhar, and net balances as empty/zero
// These will only be populated if a category is selected
$transactions = [];
$jama = [];
$udhar = [];
$net_fine = 0;
$net_amount = 0;
$net_weight = 0;

// Only fetch transactions if a category is selected
if ($category) {
    // Fetch transactions with date filtering
    $query = "SELECT * FROM transactions WHERE jeweller_id = ? AND customer_id = ? AND category = ?";
    $params = [$jeweller_id, $customer_id, $category];

    if ($from_date && $to_date) {
        $query .= " AND date BETWEEN ? AND ?";
        $params[] = $from_date;
        $params[] = $to_date;
    }

    $query .= " ORDER BY date ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();

    // Split into jama / udhar
    foreach ($transactions as $tx) {
        if ($tx['type'] === 'jama') {
            $jama[] = $tx;
        } else {
            $udhar[] = $tx;
        }
    }

    function total($list, $field) {
        return array_reduce($list, fn($carry, $item) => $carry + $item[$field], 0);
    }

    $net_fine = total($jama, 'fine') - total($udhar, 'fine');
    $net_amount = total($jama, 'amount') - total($udhar, 'amount');
    $net_weight = total($jama, 'weight') - total($udhar, 'weight');
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ledger - <?= htmlspecialchars($customer_name) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 0;
      margin: 0;
      background-color: #f4f7f6;
      color: #333;
    }
    .header {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      background-color: #fff;
      border-bottom: 1px solid #eee;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .header .back-link {
      font-size: 1.5em;
      color: #007bff;
      margin-right: 15px;
      text-decoration: none;
    }
    .header h2 {
      margin: 0;
      color: #333;
      font-weight: 600;
    }
    .container {
      max-width: 1000px;
      margin: 20px auto;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 25px;
    }
    .category-buttons {
      text-align: center;
      margin-bottom: 25px;
      display: flex;
      justify-content: center;
      gap: 10px; /* Space between buttons */
    }
    .category-buttons a {
      flex: 1; /* Distribute space equally */
      display: block; /* Make the anchor fill the flex item */
      padding: 12px 20px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 25px; /* Pill shape */
      font-weight: bold;
      transition: background-color 0.3s ease, transform 0.2s ease;
      max-width: 150px; /* Limit width on desktop */
    }
    .category-buttons a:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }
    .category-buttons a.active {
      background-color: #0056b3;
      box-shadow: inset 0 2px 5px rgba(0,0,0,0.2);
    }

    form.filter {
      text-align: center;
      margin-bottom: 25px;
      padding: 15px;
      background-color: #f9f9f9;
      border-radius: 8px;
      display: flex;
      flex-wrap: wrap; /* Allow wrapping on small screens */
      justify-content: center;
      align-items: center;
      gap: 15px; /* Space between filter elements */
    }
    form.filter label {
      font-weight: 500;
      color: #555;
    }
    form.filter input[type="date"] {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1em;
      flex-grow: 1; /* Allow input to grow */
      min-width: 120px; /* Ensure minimum width for dates */
    }
    form.filter button {
      padding: 10px 25px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      font-size: 1em;
      font-weight: bold;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    form.filter button:hover {
      background-color: #218838;
      transform: translateY(-1px);
    }

    .info-category {
      text-align: center;
      font-size: 1.1em;
      margin-bottom: 20px;
      color: #666;
      font-weight: 500;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      overflow-x: auto; /* For desktop, if table content is wide */
      display: block; /* To make overflow-x work */
    }
    th, td {
      border: 1px solid #e0e0e0;
      padding: 12px;
      text-align: center;
      white-space: nowrap; /* Prevent wrapping in table cells */
    }
    th {
      background: #e9ecef;
      font-weight: 600;
      color: #495057;
      position: sticky; /* Sticky header for better scrolling */
      top: 0;
      z-index: 1; /* Ensure header stays above content */
    }
    tbody tr:nth-child(even) {
        background-color: #f6f6f6;
    }
    tbody tr:hover {
        background-color: #eef;
    }

    .summary {
      margin-top: 30px;
      background-color: #e9f7ef; /* Light green for positive feel */
      padding: 20px;
      border-radius: 8px;
      font-size: 1.1em;
      line-height: 1.6;
      border: 1px solid #d4edda;
    }
    .summary strong {
      color: #28a745;
    }
    .summary em {
      font-size: 0.9em;
      color: #777;
    }

    .message {
        text-align: center;
        margin-top: 50px;
        font-size: 1.2em;
        color: #555;
        background-color: #e6f7ff;
        padding: 30px;
        border-radius: 8px;
        border: 1px solid #b3e0ff;
    }

    .orientation-message {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        color: white;
        text-align: center;
        padding-top: 30%; /* Adjust as needed */
        font-size: 1.5em;
        z-index: 1000; /* Ensure it's on top */
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .orientation-message i {
        font-size: 3em;
        margin-bottom: 20px;
    }
    .orientation-message p {
        margin: 0 20px;
    }

    /* Mobile specific styles */
    @media (max-width: 768px) {
      body {
        padding: 0;
      }
      .container {
        margin: 10px auto;
        padding: 15px;
        border-radius: 0;
        box-shadow: none;
      }
      .header {
        padding: 10px 15px;
      }
      .header h2 {
        font-size: 1.3em;
      }
      .category-buttons {
        flex-direction: column; /* Stack buttons vertically on mobile */
        gap: 8px;
      }
      .category-buttons a {
        max-width: 100%; /* Full width for buttons on mobile */
      }
      form.filter {
        flex-direction: column; /* Stack filter elements vertically */
        gap: 10px;
      }
      form.filter input[type="date"],
      form.filter button {
        width: 100%;
      }
      table {
        /* On small screens, table will be scrollable horizontally */
        width: max-content; /* Allow table to be wider than screen */
      }

      /* Show orientation message for landscape on phones */
      @media (orientation: portrait) and (max-width: 768px) {
        .orientation-message {
            display: flex; /* Show message when in portrait on mobile */
        }
      }
    }
  </style>
</head>
<body>

<div class="orientation-message">
    <i class="fas fa-mobile-alt"></i>
    <p>For a better viewing experience, please rotate your device to landscape mode.</p>
</div>

<div class="header">
  <a href="dashboard.php?jeweller_id=<?= htmlspecialchars($jeweller_id) ?>&customer_id=<?= htmlspecialchars($customer_id) ?>" class="back-link" title="Go to Dashboard">
    <i class="fas fa-arrow-left"></i>
  </a>
  <h2><?= htmlspecialchars($customer_name) ?></h2>
</div>

<div class="container">

  <div class="category-buttons">
    <a href="?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer_id ?>&category=gold" class="<?= $category === 'gold' ? 'active' : '' ?>">Gold</a>
    <a href="?jeweller_id=<?= $jeweller_id ?>&customer_id=<?= $customer_id ?>&category=silver" class="<?= $category === 'silver' ? 'active' : '' ?>">Silver</a>
  </div>

  <?php if ($category): // Show filter form and data only if a category is selected ?>
  <form class="filter" method="get">
    <input type="hidden" name="jeweller_id" value="<?= $jeweller_id ?>">
    <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
    <input type="hidden" name="category" value="<?= $category ?>">
    <label for="from_date">From:</label> <input type="date" id="from_date" name="from" value="<?= $from_date ?>">
    <label for="to_date">To:</label> <input type="date" id="to_date" name="to" value="<?= $to_date ?>">
    <button type="submit">Filter</button>
  </form>

  <p class="info-category"><strong>CATEGORY:</strong> <?= strtoupper($category) ?></p>

  <table>
    <thead>
      <tr>
        <th colspan="4">PURCHASE (JAMA)</th>
        <th colspan="4">SALES (UDHAR)</th>
      </tr>
      <tr>
        <th>Date</th><th>Fine</th><th>Amount</th><th>Weight</th>
        <th>Date</th><th>Fine</th><th>Amount</th><th>Weight</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $rows = max(count($jama), count($udhar));
      for ($i = 0; $i < $rows; $i++) {
          echo "<tr>";
          // Jama
          if (isset($jama[$i])) {
              echo "<td>{$jama[$i]['date']}</td><td>{$jama[$i]['fine']}</td><td>{$jama[$i]['amount']}</td><td>{$jama[$i]['weight']}</td>";
          } else {
              echo "<td></td><td></td><td></td><td></td>";
          }
          // Udhar
          if (isset($udhar[$i])) {
              echo "<td>{$udhar[$i]['date']}</td><td>{$udhar[$i]['fine']}</td><td>{$udhar[$i]['amount']}</td><td>{$udhar[$i]['weight']}</td>";
          } else {
              echo "<td></td><td></td><td></td><td></td>";
          }
          echo "</tr>";
      }
      ?>
    </tbody>
  </table>

  <div class="summary">
    <strong>NET BALANCE FINE:</strong> <?= number_format($net_fine, 2) ?> grams<br>
    <strong>NET BALANCE AMOUNT:</strong> ₹<?= number_format($net_amount, 2) ?><br>
    <strong>NET BALANCE WEIGHT:</strong> <?= number_format($net_weight, 2) ?> grams<br>
    <em>(Balance may be negative)</em>
  </div>

  <?php else: // Show message if no category is selected ?>
    <div class="message">
        Please choose a category (Gold or Silver) from above to view the ledger.
    </div>
  <?php endif; ?>

</div>

</body>
</html>