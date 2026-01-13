<?php
/**
 * resetPassword.php
 *
 * Responsibilities:
 * - Validate password reset requests
 * - Verify reset token against the stored user record
 * - Update the user's password securely
 */

session_start();

include '../includes/settings.php';
include '../includes/conn.php';

/**
 * Guard clause: ensure the reset form was submitted intentionally.
 */
if (!isset($_POST['resetPassword'])) {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: reset');
    exit;
}

/**
 * Collect POST inputs.
 * Values are validated before use.
 */
$code        = $_POST['code'] ?? '';
$userId      = $_POST['user'] ?? '';
$newPass     = $_POST['newPass'] ?? '';
$confirmPass = $_POST['confirmPass'] ?? '';

/**
 * Validate required fields.
 */
if (empty($code) || empty($userId) || empty($newPass) || empty($confirmPass)) {
    $_SESSION['error'] = 'All fields are required.';
    header("Location: reset?code={$code}&user={$userId}");
    exit;
}

/**
 * Open database connection.
 */
$pdo  = new Database();
$conn = $pdo->open();

/**
 * Fetch user record and validate reset code.
 */
$stmt = $conn->prepare("SELECT userID, resetCode FROM users WHERE userID = :userID");
$stmt->execute(['userID' => $userId]);
$user = $stmt->fetch();

if (!$user || $user['resetCode'] !== $code) {
    $_SESSION['error'] = 'Invalid or expired reset code.';
    header("Location: reset?code={$code}&user={$userId}");
    exit;
}

/**
 * Ensure passwords match.
 */
if ($newPass !== $confirmPass) {
    $_SESSION['error'] = 'Passwords do not match.';
    header("Location: reset?code={$code}&user={$userId}");
    exit;
}

/**
 * Hash and update the new password.
 */
$hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("
        UPDATE users
        SET password = :password,
            resetCode = NULL
        WHERE userID = :userID
    ");
    $stmt->execute([
        'password' => $hashedPassword,
        'userID'   => $userId
    ]);

    $_SESSION['success'] = 'Your password has been successfully changed!';
    header('Location: index');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to update password.';
    header("Location: reset?code={$code}&user={$userId}");
    exit;
}
