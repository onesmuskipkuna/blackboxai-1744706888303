<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/Student.php';
require_once '../../classes/FeeStructure.php';
require_once '../../classes/Invoice.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$studentObj = new Student();
$feeStructureObj = new FeeStructure();
$invoiceObj = new Invoice();

$error = '';
$success = '';
$feeItems = [];
$studentId = $_GET['student_id'] ?? '';

if ($studentId) {
    // Fetch student info and fee items for current class and term (default term 1)
    $studentSql = "SELECT * FROM students WHERE id = ?";
    $stmt = $studentObj->db->query($studentSql, [$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $term = $_GET['term'] ?? 'Term 1';
    if ($student) {
        $feeItems = $feeStructureObj->getFeeStructureByClassAndTerm($student['class'], $term);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? '';
    $term = $_POST['term'] ?? '';
    $academicYear = $_POST['academic_year'] ?? '';
    $selectedFeeItems = $_POST['fee_items'] ?? [];

    if ($studentId && $term && $academicYear && !empty($selectedFeeItems)) {
        $feeItemsToInvoice = [];
        foreach ($selectedFeeItems as $feeItemId) {
            // Find fee item details
            foreach ($feeItems as $item) {
                if ($item['id'] == $feeItemId) {
                    $feeItemsToInvoice[] = [
                        'fee_item_id' => $item['id'],
                        'amount' => $item['amount']
                    ];
                    break;
                }
            }
        }
        if ($invoiceObj->createInvoice($studentId, $term, $academicYear, $feeItemsToInvoice)) {
            $success = 'Invoice created successfully.';
        } else {
            $error = 'Failed to create invoice.';
        }
    } else {
        $error = 'Please select fee items to invoice.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Invoice - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Create Invoice</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="create_invoice.php" class="space-y-4">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($studentId); ?>" />
            <div>
                <label for="term" class="block font-semibold mb-1">Term</label>
                <select id="term" name="term" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="Term 1" <?php if (($term ?? '') === 'Term 1') echo 'selected'; ?>>Term 1</option>
                    <option value="Term 2" <?php if (($term ?? '') === 'Term 2') echo 'selected'; ?>>Term 2</option>
                    <option value="Term 3" <?php if (($term ?? '') === 'Term 3') echo 'selected'; ?>>Term 3</option>
                </select>
            </div>
            <div>
                <label for="academic_year" class="block font-semibold mb-1">Academic Year</label>
                <input type="text" id="academic_year" name="academic_year" required placeholder="e.g. 2024" class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Fee Items</label>
                <?php if (empty($feeItems)): ?>
                    <p class="text-gray-600">No fee items found for the student's class and selected term.</p>
                <?php else: ?>
                    <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-300 rounded p-2">
                        <?php foreach ($feeItems as $item): ?>
                            <div>
                                <label>
                                    <input type="checkbox" name="fee_items[]" value="<?php echo htmlspecialchars($item['id']); ?>" />
                                    <?php echo htmlspecialchars($item['fee_item']); ?> - <?php echo number_format($item['amount'], 2); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Create Invoice</button>
        </form>
    </div>
</body>
</html>
