<?php
require 'db.php'; // Ensure this path is correct for your database connection

$jeweller_id = $_GET['jeweller_id'] ?? null;
$customer_id = $_GET['customer_id'] ?? null;
$type = $_GET['type'] ?? null;
$category = $_GET['category'] ?? null;

if (!$jeweller_id || !$customer_id || !$type || !$category) {
    die("Invalid request. Missing jeweller_id, customer_id, type, or category.");
}

$success = false;
$error = '';
$last_entry = null;

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $fine = $_POST['fine'] ?? 0;
    $weight = $_POST['weight'] ?? 0;
    $amount = $_POST['amount'] ?? 0;

    // Convert empty strings to 0 for numerical fields
    $fine = $fine === '' ? 0 : (float)$fine;
    $weight = $weight === '' ? 0 : (float)$weight;
    $amount = $amount === '' ? 0 : (float)$amount;

    if ($date) {
        try {
            $stmt = $pdo->prepare("INSERT INTO transactions (jeweller_id, customer_id, type, category, date, fine, weight, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$jeweller_id, $customer_id, $type, $category, $date, $fine, $weight, $amount]);
            $success = true;
        } catch (PDOException $e) {
            $error = "❌ Failed to save transaction: " . $e->getMessage();
        }
    } else {
        $error = "❗ Please select a date.";
    }
}

// Fetch last 5 entries
$recent = [];
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE jeweller_id = ? AND customer_id = ? AND type = ? AND category = ? ORDER BY date DESC, id DESC LIMIT 5");
$stmt->execute([$jeweller_id, $customer_id, $type, $category]);
$recent = $stmt->fetchAll();

// Fetch latest (for dropdown repeat)
if (count($recent) > 0) {
    $last_entry = $recent[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter Transaction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }
    .container {
      max-width: 700px;
      margin-top: 40px;
      margin-bottom: 40px;
      padding: 30px;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    .form-label {
      font-weight: bold;
      margin-top: 10px;
    }
    .btn-group .btn {
      flex-grow: 1; /* Make buttons take equal width in a flex container */
    }
    .table-responsive {
      margin-top: 25px;
    }
    /* Custom styling for the select dropdown to match form inputs */
    #repeatLast {
      height: calc(1.5em + 0.75rem + 2px); /* Bootstrap's form-control height */
      padding: 0.375rem 0.75rem;
      font-size: 1rem;
      border-radius: 0.25rem;
      border: 1px solid #ced4da;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4 text-center">Enter <?= ucfirst(htmlspecialchars($type)) ?> - <?= ucfirst(htmlspecialchars($category)) ?> Transaction</h2>

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      ✅ Transaction saved successfully!
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($error) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if ($last_entry): ?>
    <div class="mb-3">
      <label for="repeatLast" class="form-label">Repeat Last Entry:</label>
      <select id="repeatLast" class="form-select">
        <option value="">-- Select Last Entry --</option>
        <option
          data-date="<?= htmlspecialchars($last_entry['date']) ?>"
          data-fine="<?= htmlspecialchars($last_entry['fine']) ?>"
          data-weight="<?= htmlspecialchars($last_entry['weight']) ?>"
          data-amount="<?= htmlspecialchars($last_entry['amount']) ?>"
        >
          <?= htmlspecialchars($last_entry['date']) ?> | Fine: <?= htmlspecialchars($last_entry['fine']) ?> | ₹<?= htmlspecialchars($last_entry['amount']) ?> | <?= htmlspecialchars($last_entry['weight']) ?>g
        </option>
      </select>
    </div>
  <?php endif; ?>

  <hr class="my-4">

  <form method="POST" id="txnForm">
    <div class="mb-3">
      <label for="date" class="form-label">Date:</label>
      <input type="date" class="form-control" id="date" name="date" required>
    </div>

    <div class="mb-3">
      <label for="fine" class="form-label">Fine (in grams):</label>
      <input type="number" class="form-control" id="fine" name="fine" step="0.01" placeholder="Fine in grams">
    </div>

    <div class="mb-3">
      <label for="weight" class="form-label">Weight (in grams):</label>
      <input type="number" class="form-control" id="weight" name="weight" step="0.01" placeholder="Weight in grams">
    </div>

    <div class="mb-3">
      <label for="amount" class="form-label">Amount (in ₹):</label>
      <input type="number" class="form-control" id="amount" name="amount" step="0.01" placeholder="Amount in ₹">
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
      <button type="submit" class="btn btn-primary me-md-2">💾 Save Transaction</button>
      <button type="reset" class="btn btn-secondary">🔄 Reset</button>
    </div>
  </form>

  <?php if (count($recent) > 0): ?>
    <h3 class="mt-5 mb-3 text-center">🕒 Recent Entries</h3>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Date</th>
            <th>Fine</th>
            <th>Weight</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['date']) ?></td>
              <td><?= htmlspecialchars($r['fine']) ?></td>
              <td><?= htmlspecialchars($r['weight']) ?></td>
              <td>₹<?= htmlspecialchars($r['amount']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="d-flex justify-content-center gap-3 mt-4">
    <a href="dashboard.php?jeweller_id=<?= htmlspecialchars($jeweller_id) ?>" class="btn btn-outline-primary">← Back to Dashboard</a>
    <a href="view_ledger.php?jeweller_id=<?= htmlspecialchars($jeweller_id) ?>&customer_id=<?= htmlspecialchars($customer_id) ?>" class="btn btn-outline-info">📊 View Ledger</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
document.getElementById("repeatLast")?.addEventListener("change", function() {
  const selected = this.options[this.selectedIndex];
  if (!selected.dataset.date) {
    // Clear fields if "-- Select Last Entry --" is chosen
    document.getElementById("date").value = '';
    document.getElementById("fine").value = '';
    document.getElementById("weight").value = '';
    document.getElementById("amount").value = '';
    return;
  }

  document.getElementById("date").value = selected.dataset.date;
  document.getElementById("fine").value = selected.dataset.fine;
  document.getElementById("weight").value = selected.dataset.weight;
  document.getElementById("amount").value = selected.dataset.amount;
});
</script>

</body>
</html>