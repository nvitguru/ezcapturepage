<?php
/**
 * editPassword.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Validate current password
 * - Update password hash for the authenticated user
 *
 * Security:
 * - POST-only
 * - No password sanitization filters (treat as opaque strings)
 * - Does not leak DB errors to the user
 */

/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Redirect helper
// -----------------------------------------------------------------------------
$redirect = static function (string $to): void {
    header('Location: ' . $to);
    exit;
};

// -----------------------------------------------------------------------------
// Auth gate
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user']) || empty($user['userID'])) {
    $redirect('index.php');
}

// -----------------------------------------------------------------------------
// POST-only + expected submit key
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['editPassword'])) {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('password');
}

// -----------------------------------------------------------------------------
// Inputs (do NOT sanitize passwords; just cast to string)
// -----------------------------------------------------------------------------
$currentPass = isset($_POST['currentPass']) ? (string)$_POST['currentPass'] : '';
$newPass     = isset($_POST['newPass']) ? (string)$_POST['newPass'] : '';
$confirmPass = isset($_POST['confirmPass']) ? (string)$_POST['confirmPass'] : '';

// Basic validation (keeps UX sane; does not change routes)
if ($newPass === '' || $confirmPass === '' || $currentPass === '') {
    $_SESSION['error'] = 'Please complete all password fields.';
    $redirect('password');
}

if ($newPass !== $confirmPass) {
    $_SESSION['error'] = 'Oops! Your passwords did not match. Please try again.';
    $redirect('password');
}

// Optional minimum length (safe + interview-friendly)
if (strlen($newPass) < 8) {
    $_SESSION['error'] = 'Your new password must be at least 8 characters.';
    $redirect('password');
}

// Verify current password BEFORE opening a transaction
if (!password_verify($currentPass, (string)$user['password'])) {
    $_SESSION['error'] = 'Oops! You have entered the wrong current password. Please try again.';
    $redirect('password');
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('editPassword.php DB connect error: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed. Please try again.';
    $redirect('password');
}

// -----------------------------------------------------------------------------
// Update password
// -----------------------------------------------------------------------------
try {
    $conn->beginTransaction();

    // PASSWORD_DEFAULT preserved to avoid environment compatibility issues.
    $passNew = password_hash($newPass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE users
        SET password = :password
        WHERE userID = :userID
        LIMIT 1
    ");
    $stmt->execute([
        'password' => $passNew,
        'userID'   => (int)$user['userID'],
    ]);

    $conn->commit();

    $_SESSION['success'] = 'Your password was successfully changed!';
    $pdo->close();
    $redirect('password');
} catch (PDOException $ex) {
    if ($conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log('editPassword.php update error (userID=' . (int)$user['userID'] . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while updating your password.';
    $pdo->close();
    $redirect('password');
}
