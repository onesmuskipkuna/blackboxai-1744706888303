<?php
require_once '../../config/config.php';
require_once '../../classes/User.php';
require_once '../../classes/Student.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

$student = new Student();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedStudents = $_POST['selected_students'] ?? [];
    $currentClass = $_POST['current_class'] ?? '';
    $nextClass = $_POST['next_class'] ?? '';

    if (empty($selectedStudents)) {
        $error = 'No students selected for promotion.';
    } elseif (!$currentClass || !$nextClass) {
        $error = 'Please select current and next class.';
    } else {
        $promotedCount = 0;
        foreach ($selectedStudents as $studentId) {
            if ($student->promoteStudent($studentId, $nextClass)) {
                $promotedCount++;
            }
        }
        $success = "$promotedCount student(s) promoted successfully.";
    }
}

// Fetch students for a class (for simplicity, primary classes only here)
$classes = $student->getClassesByLevel('primary');
$studentsList = []; // You would fetch students from DB here based on selected class

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Promote Students - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Promote Students</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="promote_students.php" class="space-y-4">
            <div>
                <label for="current_class" class="block font-semibold mb-1">Current Class</label>
                <select id="current_class" name="current_class" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Current Class</option>
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?php echo htmlspecialchars($cls); ?>"><?php echo htmlspecialchars($cls); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="next_class" class="block font-semibold mb-1">Next Class</label>
                <select id="next_class" name="next_class" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Next Class</option>
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?php echo htmlspecialchars($cls); ?>"><?php echo htmlspecialchars($cls); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Select Students</label>
                <div class="max-h-64 overflow-y-auto border border-gray-300 rounded p-2">
                    <!-- For demo, no students loaded. In real app, load students via AJAX based on current_class -->
                    <p class="text-gray-500">Select a current class to load students.</p>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Promote Selected Students</button>
        </form>
    </div>
    <script>
        // TODO: Implement AJAX to load students based on current_class selection
    </script>
</body>
</html>
