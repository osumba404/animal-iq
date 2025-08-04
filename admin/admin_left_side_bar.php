<?php
// admin/admin_left_side_bar.php

function isActive($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
            .sidebar-header {
                padding: 1.5rem;
                border-bottom: 1px solid var(--color-primary-accent);
                margin-bottom: 1rem;
                position: sticky;
                top: 0;
                background: var(--color-primary-dark);
                z-index: 1;
            }

            .sidebar-header h2 {
                margin: 0;
                font-size: 1.5rem;
                font-weight: 500;
                color: var(--color-primary-light);
                position: relative;
                display: inline-block;
            }

            .sidebar-header h2::after {
                content: '';
                position: absolute;
                bottom: -5px;
                left: 0;
                width: 100%;
                height: 2px;
                background: var(--color-primary-accent);
                transform: scaleX(0);
                transform-origin: right;
                transition: transform 0.3s ease;
            }

            .sidebar-header:hover h2::after {
                transform: scaleX(1);
                transform-origin: left;
            }

            .admin-nav {
                flex: 1;
                padding: 0 1rem;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                overflow-y: auto;
                scrollbar-width: none;
            }
            .admin-nav::-webkit-scrollbar {
                    display: none;
                }

                /* Hide scrollbar for IE and Edge */
                .admin-nav {
                    -ms-overflow-style: none;
                }


            .nav-section {
                margin-bottom: 1rem;
                animation: fadeIn 0.4s ease forwards;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateX(-10px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .nav-section:nth-child(2) { animation-delay: 0.1s; }
            .nav-section:nth-child(3) { animation-delay: 0.2s; }

            .nav-section-title {
                font-size: 0.8rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: var(--color-primary-mid);
                margin-bottom: 0.75rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid var(--color-primary-accent);
            }

            .admin-nav a {
                display: flex;
                align-items: center;
                padding: 0.75rem 1rem;
                margin-bottom: 0.25rem;
                color: var(--color-primary-light);
                text-decoration: none;
                border-radius: 4px;
                transition: var(--sidebar-transition);
                background: transparent;
            }

            .admin-nav a i {
                width: 24px;
                text-align: center;
                margin-right: 0.75rem;
                font-size: 1rem;
                color: var(--color-primary-mid);
                transition: var(--sidebar-transition);
            }

            .admin-nav a:hover {
                background: rgba(79, 93, 47, 0.2); /* primary-accent with opacity */
                transform: translateX(5px);
            }

            .admin-nav a:hover i {
                color: var(--color-primary-light);
            }

            .admin-nav a.active {
                background: rgba(79, 93, 47, 0.3); /* primary-accent with more opacity */
                box-shadow: inset 3px 0 0 var(--color-primary-accent);
                font-weight: 500;
            }

            .admin-nav a.active i {
                color: var(--color-primary-light);
            }

            .sidebar-footer {
                padding: 1rem;
                margin-top: auto;
                border-top: 1px solid var(--color-primary-accent);
                position: sticky;
                bottom: 0;
                background: var(--color-primary-dark);
            }

            .return-site {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0.75rem;
                color: var(--color-primary-light);
                text-decoration: none;
                border-radius: 4px;
                transition: var(--sidebar-transition);
                background: rgba(79, 93, 47, 0.2); /* primary-accent with opacity */
            }

            .return-site i {
                margin-right: 0.5rem;
                transition: var(--sidebar-transition);
            }

            .return-site:hover {
                background: rgba(79, 93, 47, 0.4); /* primary-accent with more opacity */
            }

            .return-site:hover i {
                transform: translateX(-3px);
            }

            /* Ensure main content doesn't hide behind sidebar */
            .main-content {
                margin-left: var(--sidebar-width);
                padding: 2rem;
                min-height: 100vh;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .admin-sidebar {
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }
                
                .admin-sidebar.active {
                    transform: translateX(0);
                }
                
                .main-content {
                    margin-left: 0;
                }
            }
    </style>
</head>
<body>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <nav class="admin-nav">
        <div class="nav-section">
            <h3 class="nav-section-title">Content</h3>
            <a href="manage_posts.php" class="<?= isActive('manage_posts.php') ?>">
                <i class="fas fa-newspaper"></i> Manage Posts
            </a>
            <a href="manage_animals.php" class="<?= isActive('manage_animals.php') ?>">
                <i class="fas fa-paw"></i> Manage Animals
            </a>
            <!-- <a href="manage_podcasts.php" class="<?= isActive('manage_podcasts.php') ?>">
                <i class="fas fa-podcast"></i> Manage Podcasts
            </a> -->
            <!-- <a href="manage_knowledge.php" class="<?= isActive('manage_knowledge.php') ?>">
                <i class="fas fa-book"></i> Indigenous Knowledge
            </a> -->
        </div>
        
        <div class="nav-section">
            <h3 class="nav-section-title">Community</h3>
            <a href="manage-users.php" class="<?= isActive('manage-users.php') ?>">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <!-- <a href="manage_forum.php" class="<?= isActive('manage_forum.php') ?>">
                <i class="fas fa-comments"></i> Manage Forum
            </a> -->
            <a href="manage_events.php" class="<?= isActive('manage_events.php') ?>">
                <i class="fas fa-calendar-alt"></i> Manage Events
            </a>
            <a href="manage_badges.php" class="<?= isActive('manage_badges.php') ?>">
                <i class="fas fa-award"></i> Manage Badges
            </a>
        </div>
        
        <div class="nav-section">
            <h3 class="nav-section-title">System</h3>
            <a href="manage_quizzes.php" class="<?= isActive('manage_quizzes.php') ?>">
                <i class="fas fa-question-circle"></i> Manage Quizzes
            </a>
            <a href="manage_species_statuses.php" class="<?= isActive('manage_species_statuses.php') ?>">
                <i class="fas fa-list-ol"></i> Species Statuses
            </a>
            <a href="manage_taxonomy.php" class="<?= isActive('manage_taxonomy.php') ?>">
                <i class="fas fa-sitemap"></i> Manage Taxonomy
            </a>
            <a href="site_settings.php" class="<?= isActive('site_settings.php') ?>">
                <i class="fas fa-cog"></i> Site Settings
            </a>
            <a href="logs.php" class="<?= isActive('logs.php') ?>">
                <i class="fas fa-clipboard-list"></i> View Logs
            </a>
        </div>
    </nav>
    <div class="sidebar-footer">
        <a href="../index.php" class="return-site">
            <i class="fas fa-arrow-left"></i> Return to Site
        </a>
    </div>
</aside>
</body>
</html>