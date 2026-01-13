<?php
/**
 * editProfile.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Validate and update the authenticated user's profile fields (fname, lname, email)
 * - Redirect back to profile with a flash message
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['editProfile'])) {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('profile');
}

// -----------------------------------------------------------------------------
// Inputs
// -----------------------------------------------------------------------------
$fname = isset($_POST['fname']) ? trim((string)$_POST['fname']) : '';
$lname = isset($_POST['lname']) ? trim((string)$_POST['lname']) : '';
$email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';

// Basic validation
if ($fname === '' || $lname === '' || $email === '') {
    $_SESSION['error'] = 'Please complete all profile fields.';
    $redirect('profile');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    $redirect('profile');
}

// Optional: light normalization (avoid weird whitespace)
$fname = preg_replace('/\s+/', ' ', $fname);
$lname = preg_replace('/\s+/', ' ', $lname);

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('editProfile.php DB connect error: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed. Please try again.';
    $redirect('profile');
}

// -----------------------------------------------------------------------------
// Update profile (scoped to authenticated user)
// -----------------------------------------------------------------------------
try {
    // Transaction not required for a single UPDATE, but keeping the flow simple/consistent is fine.
    $conn->beginTransaction();

    $updateStmt = $conn->prepare("
        UPDATE users
        SET
            fname = :fname,
            lname = :lname,
            email = :email
        WHERE userID = :userID
        LIMIT 1
    ");

    $updateStmt->execute([
        'fname'  => $fname,
        'lname'  => $lname,
        'email'  => $email,
        'userID' => (int)$user['userID'],
    ]);

    $conn->commit();

    $_SESSION['success'] = 'Member profile has been updated successfully!';
    $pdo->close();
    $redirect('profile');
} catch (PDOException $ex) {
    if ($conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log('editProfile.php update error (userID=' . (int)$user['userID'] . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while updating your profile.';
    $pdo->close();
    $redirect('profile');
}
