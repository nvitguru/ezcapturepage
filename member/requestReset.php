<?php
/**
 * requestReset.php
 *
 * Responsibilities:
 * - Handle password reset requests
 * - Generate and store a reset token
 * - Send reset email to the account email address
 */

session_start();

require '../vendor/autoload.php';
require '../includes/settings.php';
require '../includes/conn.php';

/**
 * Only accept valid POST requests from reset form.
 */
if (!isset($_POST['requestReset'])) {
    $_SESSION['error'] = 'Input email associated with account.';
    header('Location: forgot-password');
    exit;
}

/**
 * Normalize and validate email input.
 */
$email = filter_input(INPUT_POST, 'forgotEmail', FILTER_VALIDATE_EMAIL);
if (!$email) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    header('Location: forgot-password');
    exit;
}

$conn = $pdo->open();

/**
 * Look up user by email.
 */
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = 'Oops! The email was not found in our system.';
    $pdo->close();
    header('Location: forgot-password');
    exit;
}

/**
 * Generate secure reset token.
 * Length preserved for compatibility.
 */
$code = bin2hex(random_bytes(8)); // 16 characters

try {
    /**
     * Persist reset token.
     */
    $updateStmt = $conn->prepare(
        "UPDATE users SET resetCode = :code WHERE userID = :userID"
    );
    $updateStmt->execute([
        'code'   => $code,
        'userID'=> $user['userID']
    ]);

    /**
     * Build reset email.
     */
    $resetUrl = "https://" . SYSTEM_URL . "/member/reset?code={$code}&user={$user['userID']}";

    $message = "
        <h2>Password Reset</h2>
        <p>A password reset was requested for your " . SYSTEM_NAME . " account.</p>
        <p><strong>Account Email:</strong><br>{$email}</p>
        <p>Please click the link below to reset your password:</p>
        <p><a href='{$resetUrl}'>{$resetUrl}</a></p>
        <p>If you did not request this reset, you may safely ignore this email.</p>
        <h4>" . SYSTEM_NAME . " Support Team</h4>
        <p>" . SYSTEM_SUPPORT_EMAIL . "<br>" . SYSTEM_URL . "</p>
        <p>
            <img src='https://" . SYSTEM_URL . "/images/ez-capture-page-logo.png' style='max-width:200px;'>
        </p>
    ";

    /**
     * Send reset email.
     */
    $mailer = \EZCapture\Emails::getMailer();
    $mailer->addAddress($email);
    $mailer->isHTML(true);
    $mailer->Subject = SYSTEM_NAME . ' Password Reset Request';
    $mailer->Body = $message;
    $mailer->send();

    $_SESSION['success'] = 'Password reset link sent! Check your inbox or spam folder.';

} catch (Exception $e) {
    /**
     * Do not expose internal mailer or DB errors to user.
     */
    $_SESSION['error'] = 'Unable to send reset email at this time.';
}

/**
 * Cleanup and redirect.
 */
$pdo->close();
header('Location: forgot-password');
exit;
