<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/Invoice.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$invoiceObj = new Invoice();
$invoiceId = $_GET['invoice_id'] ?? '';

if (!$invoiceId) {
    die('Invoice ID is required.');
}

$invoiceSql = "SELECT i.*, s.first_name, s.last_name, s.class FROM invoices i JOIN students s ON i.student_id = s.id WHERE i.id = ?";
$stmt = $invoiceObj->db->query($invoiceSql, [$invoiceId]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    die('Invoice not found.');
}

$invoiceItems = $invoiceObj->getInvoiceItems($invoiceId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Invoice - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Invoice Details</h1>
        <div class="mb-4">
            <strong>Student:</strong> <?php echo htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']); ?><br />
            <strong>Class:</strong> <?php echo htmlspecialchars($invoice['class']); ?><br />
            <strong>Term:</strong> <?php echo htmlspecialchars($invoice['term']); ?><br />
            <strong>Academic Year:</strong> <?php echo htmlspecialchars($invoice['academic_year']); ?><br />
            <strong>Invoice Date:</strong> <?php echo htmlspecialchars($invoice['created_at']); ?><br />
            <strong>Status:</strong> <?php echo htmlspecialchars($invoice['status']); ?><br />
        </div>
        <table class="w-full border border-gray-300 mb-4">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-3 py-1 text-left">Fee Item</th>
                    <th class="border border-gray-300 px-3 py-1 text-right">Amount</th>
                    <th class="border border-gray-300 px-3 py-1 text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoiceItems as $item): ?>
                    <tr>
                        <td class="border border-gray-300 px-3 py-1"><?php echo htmlspecialchars($item['fee_item']); ?></td>
                        <td class="border border-gray-300 px-3 py-1 text-right"><?php echo number_format($item['amount'], 2); ?></td>
                        <td class="border border-gray-300 px-3 py-1 text-right"><?php echo number_format($item['balance'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-right font-semibold">
            Total Due: <?php echo number_format($invoice['balance'], 2); ?>
        </div>
    </div>
</body>
</html>
