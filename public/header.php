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
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        /* Header-specific styles */
        body {
            background-color: var(--color-bg-primary);
            color: var(--color-text-primary);
            margin: 0;
            padding: 0;
            font-family: Bahnschrift, 'DIN Alternate', 'Franklin Gothic Medium', 'Nimbus Sans Narrow', sans-serif-condensed, sans-serif;
        }
        
        .header-container {
            background-color: var(--color-primary-dark);
            color: var(--color-text-inverted);
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--color-text-inverted);
            font-weight: bold;
            font-size: 1.25rem;
        }
        
        .brand-logo:hover {
            color: var(--color-accent-primary);
        }
        
        .user-initial-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--color-accent-primary);
            color: var(--color-primary-dark);
            font-weight: bold;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <!-- <a href="/public/index.php" class="brand-logo">
            Animal IQ
        </a> -->
        
        <?php if (!empty($userInitial)): ?>
            <div class="user-initial-circle"><?= $userInitial ?></div>
        <?php endif; ?>
    </div>