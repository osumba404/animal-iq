<!-- public/header.php -->

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php'; // adjust path as needed

// Set default page title
if (!isset($page_title)) {
    $page_title = "Animal IQ";
}

// Get logged-in user's first name initial
$userInitial = '';
if (isset($_SESSION['user']) && !empty($_SESSION['user']['name'])) {
    $userInitial = strtoupper(substr($_SESSION['user']['name'], 0, 1));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
</head>
<body class="bg-gray-100 text-gray-900">

<!-- <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <a href="/public/index.php" class="text-2xl font-bold text-blue-600 hover:text-blue-800 transition">
            Animal IQ
        </a>

    </div>
</header> -->
