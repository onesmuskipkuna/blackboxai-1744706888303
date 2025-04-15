<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/Report.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$report = new Report();

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');
$reportType = $_GET['report_type'] ?? 'daily_fee_collection';

$data = null;
$error = '';

try {
    switch ($reportType) {
        case 'daily_fee_collection':
            $data = $report->getDailyFeeCollection($startDate);
            break;
        case 'daily_expenses':
            $data = $report->getDailyExpenses($startDate);
            break;
        case 'payroll_report':
            $data = $report->getPayrollReport($startDate, $endDate);
            break;
        case 'profit_loss':
            $data = $report->getProfitAndLossReport($startDate, $endDate);
            break;
        default:
            $error = 'Invalid report type selected.';
    }
} catch (Exception $e) {
    $error = 'Error generating report: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reports - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Reports</h1>
        <form method="GET" action="reports.php" class="mb-6 space-x-4">
            <label for="start_date" class="font-semibold">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" required class="border border-gray-300 rounded px-3 py-1" />
            <label for="end_date" class="font-semibold">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" required class="border border-gray-300 rounded px-3 py-1" />
            <label for="report_type" class="font-semibold">Report Type:</label>
            <select id="report_type" name="report_type" required class="border border-gray-300 rounded px-3 py-1">
                <option value="daily_fee_collection" <?php if ($reportType === 'daily_fee_collection') echo 'selected'; ?>>Daily Fee Collection</option>
                <option value="daily_expenses" <?php if ($reportType === 'daily_expenses') echo 'selected'; ?>>Daily Expenses</option>
                <option value="payroll_report" <?php if ($reportType === 'payroll_report') echo 'selected'; ?>>Payroll Report</option>
                <option value="profit_loss" <?php if ($reportType === 'profit_loss') echo 'selected'; ?>>Profit and Loss</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">Generate</button>
        </form>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php else: ?>
            <div>
                <h2 class="text-xl font-semibold mb-2">Report Results</h2>
                <pre><?php print_r($data); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
