<?php
session_start();
require_once '../includes/db.php';

// Get form values
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Lookup user
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];

    // Redirect back to home
    header('Location: ../public/index.php');
    exit;
} else {
    // Redirect back with error message
    header("Location: ../public/login.php?error=Invalid credentials&email=" . urlencode($email));
    exit;
}
