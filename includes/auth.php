<?php
session_start();

function require_login($role_required = null) {
    if (!isset($_SESSION['user'])) {
        // Cache the requested page
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }

    if ($role_required) {
        $user_role = $_SESSION['user']['role'];
        $role_hierarchy = [
            'visitor' => 0,
            'enthusiast' => 1,
            'contributor' => 2,
            'researcher' => 3,
            'moderator' => 4,
            'admin' => 5,
            'super_admin' => 6,
        ];

        if ($role_hierarchy[$user_role] < $role_hierarchy[$role_required]) {
            echo "<h2 style='color:red;'>Access denied: Insufficient role</h2>";
            exit;
        }
    }
}
