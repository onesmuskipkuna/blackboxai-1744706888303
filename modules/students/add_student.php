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
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $guardianName = trim($_POST['guardian_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $schoolLevel = $_POST['school_level'] ?? '';
    $class = $_POST['class'] ?? '';

    if ($firstName && $lastName && $guardianName && $phone && $schoolLevel && $class) {
        if ($student->addStudent($firstName, $lastName, $guardianName, $phone, $schoolLevel, $class)) {
            $success = 'Student added successfully.';
        } else {
            $error = 'Failed to add student. Please try again.';
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}

$primaryClasses = $student->getClassesByLevel('primary');
$juniorSecondaryClasses = $student->getClassesByLevel('junior secondary');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Student - <?php echo APP_NAME; ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Add Student</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="add_student.php" class="space-y-4">
            <div>
                <label for="first_name" class="block font-semibold mb-1">First Name</label>
                <input type="text" id="first_name" name="first_name" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="last_name" class="block font-semibold mb-1">Last Name</label>
                <input type="text" id="last_name" name="last_name" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="guardian_name" class="block font-semibold mb-1">Parent/Guardian Name</label>
                <input type="text" id="guardian_name" name="guardian_name" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="phone" class="block font-semibold mb-1">Phone Number</label>
                <input type="text" id="phone" name="phone" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="school_level" class="block font-semibold mb-1">School Level</label>
                <select id="school_level" name="school_level" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Level</option>
                    <option value="primary">Primary</option>
                    <option value="junior secondary">Junior Secondary</option>
                </select>
            </div>
            <div>
                <label for="class" class="block font-semibold mb-1">Class</label>
                <select id="class" name="class" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Class</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Add Student</button>
        </form>
    </div>
    <script>
        const primaryClasses = <?php echo json_encode($primaryClasses); ?>;
        const juniorSecondaryClasses = <?php echo json_encode($juniorSecondaryClasses); ?>;

        $('#school_level').on('change', function() {
            const level = $(this).val();
            let classes = [];
            if (level === 'primary') {
                classes = primaryClasses;
            } else if (level === 'junior secondary') {
                classes = juniorSecondaryClasses;
            }
            const classSelect = $('#class');
            classSelect.empty();
            classSelect.append('<option value="">Select Class</option>');
            classes.forEach(cls => {
                classSelect.append(`<option value="${cls}">${cls}</option>`);
            });
        });
    </script>
</body>
</html>
