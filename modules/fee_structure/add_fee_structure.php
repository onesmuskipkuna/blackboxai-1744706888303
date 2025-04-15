<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/FeeStructure.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$feeStructure = new FeeStructure();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class = $_POST['class'] ?? '';
    $term = $_POST['term'] ?? '';
    $feeItem = trim($_POST['fee_item'] ?? '');
    $amount = $_POST['amount'] ?? '';

    if ($class && $term && $feeItem && is_numeric($amount)) {
        if ($feeStructure->addFeeStructure($class, $term, $feeItem, $amount)) {
            $success = 'Fee structure added successfully.';
        } else {
            $error = 'Failed to add fee structure. Please try again.';
        }
    } else {
        $error = 'Please fill in all fields correctly.';
    }
}

$classes = array_merge(
    ['PG', 'PP1', 'PP2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'],
    ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10']
);
$terms = ['Term 1', 'Term 2', 'Term 3'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Fee Structure - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Add Fee Structure</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="add_fee_structure.php" class="space-y-4">
            <div>
                <label for="class" class="block font-semibold mb-1">Class</label>
                <select id="class" name="class" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?php echo htmlspecialchars($cls); ?>"><?php echo htmlspecialchars($cls); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="term" class="block font-semibold mb-1">Term</label>
                <select id="term" name="term" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Term</option>
                    <?php foreach ($terms as $term): ?>
                        <option value="<?php echo htmlspecialchars($term); ?>"><?php echo htmlspecialchars($term); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fee_item" class="block font-semibold mb-1">Fee Item</label>
                <input type="text" id="fee_item" name="fee_item" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="amount" class="block font-semibold mb-1">Amount</label>
                <input type="number" step="0.01" id="amount" name="amount" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Add Fee Structure</button>
        </form>
    </div>
</body>
</html>
