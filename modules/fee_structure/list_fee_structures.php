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
$classes = array_merge(
    ['PG', 'PP1', 'PP2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'],
    ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10']
);
$terms = ['Term 1', 'Term 2', 'Term 3'];

$allFeeStructures = [];
foreach ($classes as $class) {
    foreach ($terms as $term) {
        $items = $feeStructure->getFeeStructureByClassAndTerm($class, $term);
        if ($items) {
            $allFeeStructures[$class][$term] = $items;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fee Structures - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Fee Structures</h1>
        <?php if (empty($allFeeStructures)): ?>
            <p class="text-gray-600">No fee structures found.</p>
        <?php else: ?>
            <?php foreach ($allFeeStructures as $class => $termsData): ?>
                <h2 class="text-xl font-semibold mt-6 mb-2"><?php echo htmlspecialchars($class); ?></h2>
                <?php foreach ($termsData as $term => $items): ?>
                    <h3 class="text-lg font-semibold mb-1"><?php echo htmlspecialchars($term); ?></h3>
                    <table class="w-full border border-gray-300 mb-4">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-3 py-1 text-left">Fee Item</th>
                                <th class="border border-gray-300 px-3 py-1 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="border border-gray-300 px-3 py-1"><?php echo htmlspecialchars($item['fee_item']); ?></td>
                                    <td class="border border-gray-300 px-3 py-1 text-right"><?php echo number_format($item['amount'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
