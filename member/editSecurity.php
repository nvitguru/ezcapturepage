<?php
/**
 * editSecurity.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Validate and update the authenticated user's security pincode
 * - Require current password confirmation
 *
 * Security:
 * - POST-only
 * - Validates pincode format (6–10 digits)
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['editSecurity'])) {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('profile');
}

// -----------------------------------------------------------------------------
// Inputs
// -----------------------------------------------------------------------------
$pincode     = isset($_POST['pincode']) ? trim((string)$_POST['pincode']) : '';
$currentPass = isset($_POST['password']) ? (string)$_POST['password'] : '';

if ($pincode === '' || $currentPass === '') {
    $_SESSION['error'] = 'Please enter your pin code and current password.';
    $redirect('profile');
}

// Validate pincode length + format: 6–10 digits
if (!preg_match('/^\d{6,10}$/', $pincode)) {
    $_SESSION['error'] = 'Pin code must be between 6 and 10 digits long.';
    $redirect('profile');
}

// Verify password BEFORE opening a transaction
if (!password_verify($currentPass, (string)$user['password'])) {
    $_SESSION['error'] = 'Incorrect password.';
    $redirect('profile');
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('editSecurity.php DB connect error: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed. Please try again.';
    $redirect('profile');
}

// -----------------------------------------------------------------------------
// Update pin code
// -----------------------------------------------------------------------------
try {
    $conn->beginTransaction();

    $updateStmt = $conn->prepare("
        UPDATE users
        SET pincode = :pincode
        WHERE userID = :userID
        LIMIT 1
    ");
    $updateStmt->execute([
        'pincode' => $pincode,
        'userID'  => (int)$user['userID'],
    ]);

    $conn->commit();

    $_SESSION['success'] = 'Security pin code has been updated successfully!';
    $pdo->close();
    $redirect('profile');
} catch (PDOException $ex) {
    if ($conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log('editSecurity.php update error (userID=' . (int)$user['userID'] . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while updating your pin code.';
    $pdo->close();
    $redirect('profile');
}
