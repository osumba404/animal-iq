<!-- public/header.php -->


<?php
// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: Include database connection here if needed globally
// require_once '../config/db.php';

// Set default title if not provided
if (!isset($page_title)) {
    $page_title = "Animal IQ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="style.css">
    <!-- You can add more shared styles or libraries here -->
</head>
<body>


<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animal IQ</title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>
<header>
    <h1><a href="/public/index.php">Animal IQ</a></h1>
</header> -->
