<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/FeeBalance.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$feeBalance = new FeeBalance();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? '';
    $fromTerm = $_POST['from_term'] ?? '';
    $toTerm = $_POST['to_term'] ?? '';
    $fromYear = $_POST['from_year'] ?? '';
    $toYear = $_POST['to_year'] ?? '';
    $fromClass = $_POST['from_class'] ?? '';
    $toClass = $_POST['to_class'] ?? '';

    if ($studentId && $fromTerm && $toTerm && $fromYear && $toYear) {
        if ($feeBalance->carryForwardTermBalance($studentId, $fromTerm, $toTerm, $fromYear, $toYear)) {
            $success = 'Term balance carried forward successfully.';
        } else {
            $error = 'Failed to carry forward term balance.';
        }
    } elseif ($studentId && $fromClass && $toClass && $toYear) {
        if ($feeBalance->carryForwardClassBalance($studentId, $fromClass, $toClass, $toYear)) {
            $success = 'Class balance carried forward successfully.';
        } else {
            $error = 'Failed to carry forward class balance.';
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Carry Forward Fee Balance - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Carry Forward Fee Balance</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="carry_forward.php" class="space-y-4">
            <div>
                <label for="student_id" class="block font-semibold mb-1">Student ID</label>
                <input type="number" id="student_id" name="student_id" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="from_term" class="block font-semibold mb-1">From Term</label>
                <input type="text" id="from_term" name="from_term" placeholder="e.g. Term 1" class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="to_term" class="block font-semibold mb-1">To Term</label>
                <input type="text" id="to_term" name="to_term" placeholder="e.g. Term 2" class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="from_year" class="block font-semibold mb-1">From Academic Year</label>
                <input type="text" id="from_year" name="from_year" placeholder="e.g. 2023" class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="to_year" class="block font-semibold mb-1">To Academic Year</label>
                <input type="text" id="to_year" name="to_year" placeholder="e.g. 2024" class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div class="border-t border-gray-300 my-4"></div>
            <div>
                <label for="from_class" class="block font-semibold mb-1">From Class</label>
                <input type="text" id="from_class" name="from_class" placeholder="e.g. Grade 6" class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="to_class" class="block font-semibold mb-1">To Class</label>
                <input type="text" id="to_class" name="to_class" placeholder="e.g. Grade 7" class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Carry Forward Balance</button>
        </form>
    </div>
</body>
</html>
