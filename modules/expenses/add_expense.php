<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/Expense.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$expense = new Expense();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['date'] ?? '';
    $amount = $_POST['amount'] ?? '';

    if ($category && $description && $date && is_numeric($amount)) {
        if ($expense->addExpense($category, $description, $date, $amount)) {
            $success = 'Expense added successfully.';
        } else {
            $error = 'Failed to add expense. Please try again.';
        }
    } else {
        $error = 'Please fill in all fields correctly.';
    }
}

$categories = ['Utility', 'Fuel', 'Transport', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Expense - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Add Expense</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="add_expense.php" class="space-y-4">
            <div>
                <label for="category" class="block font-semibold mb-1">Category</label>
                <select id="category" name="category" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="description" class="block font-semibold mb-1">Description</label>
                <input type="text" id="description" name="description" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="date" class="block font-semibold mb-1">Date</label>
                <input type="date" id="date" name="date" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="amount" class="block font-semibold mb-1">Amount</label>
                <input type="number" step="0.01" id="amount" name="amount" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Add Expense</button>
        </form>
    </div>
</body>
</html>
