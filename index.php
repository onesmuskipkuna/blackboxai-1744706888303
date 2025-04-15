<?php
require_once 'config/config.php';
require_once 'classes/User.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo APP_NAME; ?> - Dashboard</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Welcome to <?php echo APP_NAME; ?></h1>
        <p class="mb-4">You are logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>.</p>
        <a href="logout.php" class="text-blue-600 hover:underline">Logout</a>
    </div>
</body>
</html>
