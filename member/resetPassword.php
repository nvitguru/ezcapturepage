<?php
session_start();
include '../includes/settings.php';
include '../includes/conn.php';

session_start();

// Check if the form has been submitted
if (!isset($_POST['resetPassword'])) {
    $_SESSION['error'] = 'Invalid request.';
    header("Location: reset");
    exit();
}

// Get POST and GET variables
$code = $_POST['code'] ?? '';
$user = $_POST['user'] ?? '';
$newPass = $_POST['newPass'] ?? '';
$confirmPass = $_POST['confirmPass'] ?? '';

// Check if required fields are empty
if (empty($code) || empty($user) || empty($newPass) || empty($confirmPass)) {
    $_SESSION['error'] = 'All fields are required.';
    header("Location: reset?code=$code&user=$user");
    exit();
}

// Database connection
$pdo = new Database();
$conn = $pdo->open();

// Verify the reset code and user ID
$stmt = $conn->prepare("SELECT * FROM users WHERE userID = :userID");
$stmt->execute(['userID' => $user]);
$row = $stmt->fetch();

if (!$row || $row['resetCode'] !== $code) {
    $_SESSION['error'] = 'Invalid or expired reset code.';
    header("Location: reset?code=$code&user=$user");
    exit();
}

// Check if passwords match
if ($newPass !== $confirmPass) {
    $_SESSION['error'] = 'Passwords do not match.';
    header("Location: reset?code=$code&user=$user");
    exit();
}

// Update the password
$hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
try {
    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE userID = :userID");
    $stmt->execute(['password' => $hashedPassword, 'userID' => $user]);

    $_SESSION['success'] = 'Your password has been successfully changed!';
    header("Location: index");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to update password: ' . $e->getMessage();
    header("Location: reset?code=$code&user=$user");
    exit();
}
