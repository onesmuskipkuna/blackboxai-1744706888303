<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/Invoice.php';
require_once '../../classes/Payment.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$invoiceObj = new Invoice();
$paymentObj = new Payment();

$error = '';
$success = '';
$studentId = $_GET['student_id'] ?? '';
$invoices = [];
$selectedInvoiceId = $_GET['invoice_id'] ?? '';
$invoiceItems = [];

if ($studentId) {
    $invoices = $invoiceObj->getInvoicesByStudent($studentId);
}

if ($selectedInvoiceId) {
    $invoiceItems = $invoiceObj->getInvoiceItems($selectedInvoiceId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoiceId = $_POST['invoice_id'] ?? '';
    $payments = $_POST['payments'] ?? [];
    $paymentMode = $_POST['payment_mode'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');
    $createdBy = $_SESSION['user_id'] ?? 0;

    if ($invoiceId && $paymentMode && !empty($payments)) {
        if ($paymentObj->recordPayment($invoiceId, $payments, $paymentMode, $remarks, $createdBy)) {
            $success = 'Payment recorded successfully.';
            // Refresh invoice items after payment
            $invoiceItems = $invoiceObj->getInvoiceItems($invoiceId);
        } else {
            $error = 'Failed to record payment.';
        }
    } else {
        $error = 'Please fill in all required fields and enter payment amounts.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fee Payment - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Fee Payment</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="GET" action="fee_payment.php" class="mb-6">
            <label for="student_id" class="block font-semibold mb-1">Search Student by ID</label>
            <input type="number" id="student_id" name="student_id" value="<?php echo htmlspecialchars($studentId); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
            <button type="submit" class="mt-2 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">Search</button>
        </form>

        <?php if ($studentId && empty($invoices)): ?>
            <p class="text-gray-600">No invoices found for this student.</p>
        <?php endif; ?>

        <?php if (!empty($invoices)): ?>
            <form method="GET" action="fee_payment.php" class="mb-6">
                <label for="invoice_id" class="block font-semibold mb-1">Select Invoice</label>
                <select id="invoice_id" name="invoice_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Invoice</option>
                    <?php foreach ($invoices as $inv): ?>
                        <option value="<?php echo htmlspecialchars($inv['id']); ?>" <?php if ($inv['id'] == $selectedInvoiceId) echo 'selected'; ?>>
                            Invoice #<?php echo $inv['id']; ?> - <?php echo htmlspecialchars($inv['term']); ?> - Balance: <?php echo number_format($inv['balance'], 2); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <?php if (!empty($invoiceItems)): ?>
            <form method="POST" action="fee_payment.php?student_id=<?php echo htmlspecialchars($studentId); ?>&invoice_id=<?php echo htmlspecialchars($selectedInvoiceId); ?>">
                <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($selectedInvoiceId); ?>" />
                <table class="w-full border border-gray-300 mb-4">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-3 py-1 text-left">Fee Item</th>
                            <th class="border border-gray-300 px-3 py-1 text-right">Amount</th>
                            <th class="border border-gray-300 px-3 py-1 text-right">Balance</th>
                            <th class="border border-gray-300 px-3 py-1 text-right">Amount to Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoiceItems as $item): ?>
                            <tr>
                                <td class="border border-gray-300 px-3 py-1"><?php echo htmlspecialchars($item['fee_item']); ?></td>
                                <td class="border border-gray-300 px-3 py-1 text-right"><?php echo number_format($item['amount'], 2); ?></td>
                                <td class="border border-gray-300 px-3 py-1 text-right"><?php echo number_format($item['balance'], 2); ?></td>
                                <td class="border border-gray-300 px-3 py-1 text-right">
                                    <input type="number" step="0.01" min="0" max="<?php echo htmlspecialchars($item['balance']); ?>" name="payments[<?php echo $item['id']; ?>]" class="w-full border border-gray-300 rounded px-2 py-1" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="mb-4">
                    <label for="payment_mode" class="block font-semibold mb-1">Payment Mode</label>
                    <select id="payment_mode" name="payment_mode" required class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">Select Payment Mode</option>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                        <option value="mpesa">Mpesa</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="remarks" class="block font-semibold mb-1">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="3" class="w-full border border-gray-300 rounded px-3 py-2"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Submit Payment</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
