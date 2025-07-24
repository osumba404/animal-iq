<?php
// api/register.php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Get inputs
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password_raw = $_POST['password'] ?? '';
$google_id = $_POST['google_id'] ?? null;

// Validate inputs
if (empty($name) || empty($email) || empty($password_raw)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Check if user already exists
if (getUserByEmail($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
    exit;
}

// Hash password
$password = password_hash($password_raw, PASSWORD_DEFAULT);

// Insert new user
$sql = "INSERT INTO users (name, email, password, google_id, role, created_at) VALUES (?, ?, ?, ?, 'user', NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([$name, $email, $password, $google_id]);

echo json_encode(['status' => 'success', 'message' => 'Registered successfully']);
