<!-- admin/admin_header.php -->

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $page_title ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            /* Updated variables for better coordination */
            --sidebar-width: 245px;
            --admin-banner-height: 60px;
            --admin-banner-bg: var(--color-primary-dark);
            --admin-banner-text: var(--color-primary-light);
            --admin-banner-accent: var(--color-primary-accent);
            --admin-banner-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        body {
            padding-top: var(--admin-banner-height);
            display: flex;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        /* Main content area */
        .main-content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - var(--admin-banner-height));
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background-color: white;
            margin: 0;
        }

        .admin-banner {
            position: fixed;
            top: 0;
            left: var(--sidebar-width) !important;
            right: 0;
            height: var(--admin-banner-height);
            background: var(--admin-banner-bg);
            color: var(--admin-banner-text);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 90;
            box-shadow: var(--admin-banner-shadow);
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--color-primary-accent);
        }


        .admin-banner {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            left: 0;
            right: 0;
            height: var(--admin-banner-height);
            background: var(--admin-banner-bg);
            color: var(--admin-banner-text);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 90;
            box-shadow: var(--admin-banner-shadow);
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--color-primary-accent);
        }

        /* Adjust sidebar to work with new layout */
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--color-primary-dark);
            color: var(--color-primary-light);
            box-shadow: var(--sidebar-shadow);
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        .admin-nav {
            flex: 1;
            overflow-y: auto;
            padding: 0 1rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .admin-banner__welcome {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
        }

        .admin-banner__welcome-icon {
            color: var(--color-primary-mid);
            font-size: 1.2rem;
        }

        .admin-banner__name {
            font-weight: 500;
            color: var(--color-primary-light);
        }

        .admin-banner__role {
            background: var(--color-primary-accent);
            color: var(--color-primary-light);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }

        .admin-banner__actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .admin-banner__link {
            color: var(--color-primary-mid);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .admin-banner__link:hover {
            color: var(--color-primary-light);
            transform: translateY(-2px);
        }

        .admin-banner__link i {
            font-size: 1rem;
        }

        .admin-banner__divider {
            color: var(--color-primary-mid);
            opacity: 0.5;
        }

        /* Adjust for mobile */
        @media (max-width: 768px) {
            .admin-banner {
                left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Top Banner -->
    <div class="admin-banner">
        <div class="admin-banner__welcome">
            <span class="admin-banner__welcome-icon">👋</span>
            <span>Welcome, <span class="admin-banner__name"><?= htmlspecialchars($admin_name) ?></span>
            <?php if ($admin_role): ?>
                <span class="admin-banner__role"><?= $admin_role ?></span>
            <?php endif; ?>
            </span>
        </div>
        
        <div class="admin-banner__actions">
            <a href="dashboard.php" class="admin-banner__link">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <span class="admin-banner__divider">|</span>
            <a href="admin_logout.php" class="admin-banner__link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <?php include 'admin_left_side_bar.php'; ?>