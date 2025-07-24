<?php
require_once '../includes/db.php';
session_start();

$email = trim($_POST['email']);
$password = $_POST['password'];

// Fetch user from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    // Set session
    $_SESSION['user'] = [
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role']
    ];

    // Update last_login time
    $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE email = ?");
    $update->execute([$email]);

    // Redirect to homepage
    header("Location: ../public/index.php");
    exit;
} else {
    // Redirect back to login with error
    header("Location: ../public/login.php?error=invalid_credentials&email=" . urlencode($email));
    exit;
}
