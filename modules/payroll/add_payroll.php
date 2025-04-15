<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/Payroll.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$payroll = new Payroll();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffName = trim($_POST['staff_name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $basicSalary = $_POST['basic_salary'] ?? 0;
    $allowances = $_POST['allowances'] ?? 0;
    $deductions = $_POST['deductions'] ?? 0;
    $loan = $_POST['loan'] ?? 0;
    $payslipDate = $_POST['payslip_date'] ?? '';

    if ($staffName && $position && is_numeric($basicSalary) && is_numeric($allowances) && is_numeric($deductions) && is_numeric($loan) && $payslipDate) {
        if ($payroll->addPayroll($staffName, $position, $basicSalary, $allowances, $deductions, $loan, $payslipDate)) {
            $success = 'Payroll record added successfully.';
        } else {
            $error = 'Failed to add payroll record. Please try again.';
        }
    } else {
        $error = 'Please fill in all fields correctly.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Payroll - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Add Payroll</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="add_payroll.php" class="space-y-4">
            <div>
                <label for="staff_name" class="block font-semibold mb-1">Staff Name</label>
                <input type="text" id="staff_name" name="staff_name" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="position" class="block font-semibold mb-1">Position</label>
                <input type="text" id="position" name="position" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="basic_salary" class="block font-semibold mb-1">Basic Salary</label>
                <input type="number" step="0.01" id="basic_salary" name="basic_salary" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="allowances" class="block font-semibold mb-1">Allowances</label>
                <input type="number" step="0.01" id="allowances" name="allowances" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="deductions" class="block font-semibold mb-1">Deductions</label>
                <input type="number" step="0.01" id="deductions" name="deductions" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="loan" class="block font-semibold mb-1">Loan</label>
                <input type="number" step="0.01" id="loan" name="loan" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="payslip_date" class="block font-semibold mb-1">Payslip Date</label>
                <input type="date" id="payslip_date" name="payslip_date" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Add Payroll</button>
        </form>
    </div>
</body>
</html>
