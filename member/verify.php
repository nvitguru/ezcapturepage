<?php
/**
 * verify.php
 *
 * Responsibilities:
 * - Handle member login POST submissions
 * - Validate input
 * - Verify credentials and account status
 * - Establish session state and redirect appropriately
 */

session_start();

include '../includes/session.php';
include '../includes/settings.php';

/**
 * Guard clause: require a database handle from the included session/bootstrap layer.
 */
if (!isset($pdo)) {
    $_SESSION['error'] = 'Database connection not initialized.';
    header('location: index.php');
    exit;
}

/**
 * Guard clause: only accept valid login POST submissions.
 */
if (!isset($_POST['memberLogin'])) {
    $_SESSION['error'] = 'Input login information';
    header('location: index.php');
    exit;
}

/**
 * Input: validate email and safely read password.
 * - Email is validated as a properly formatted address
 * - Password is read as a raw string (never "sanitized" into something else)
 */
$email = filter_input(INPUT_POST, 'loginEmail', FILTER_VALIDATE_EMAIL);
$password = (string)($_POST['loginPassword'] ?? '');

if (!$email || $password === '') {
    $_SESSION['error'] = 'Invalid email or password format.';
    header('location: index.php');
    exit;
}

/**
 * Open database connection.
 */
try {
    $conn = $pdo->open();
} catch (PDOException $e) {
    $_SESSION['error'] = 'There was an error processing your request. Please try again later.';
    header('location: index.php');
    exit;
}

try {
    /**
     * Portal routing check:
     * If the email exists in the admin table, route the user to the admin portal.
     */
    $adminStmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM hmfic WHERE email = :email");
    $adminStmt->execute(['email' => $email]);
    $admin = $adminStmt->fetch();

    if (!empty($admin['numrows']) && (int)$admin['numrows'] > 0) {
        $_SESSION['error'] = 'You are trying to access the wrong back office portal. Please <a href="https://' . SYSTEM_NAME . '/admin">CLICK HERE</a> to get to the admin portal.';
        header("location: index.php");
        exit;
    }

    /**
     * Lookup the member record by email.
     * Note: this preserves your existing fetch style that includes COUNT(*).
     */
    $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    $numRows = (int)($user['numrows'] ?? 0);
    $isActive = !empty($user['active']); // safe even if the row is missing

    /**
     * Validate existence + status before password verification.
     */
    if ($numRows > 0 && $isActive) {
        if (password_verify($password, (string)$user['password'])) {
            /**
             * Successful login: set session user ID and redirect.
             * Note: preserves existing session key and stored field name.
             */
            $_SESSION['user'] = $user['id'];
            header('location: dashboard.php');
            exit;
        }

        $_SESSION['error'] = 'The password entered was incorrect';
        header('location: index.php');
        exit;
    }

    /**
     * Not found vs inactive messaging (preserves your original wording).
     */
    if ($numRows > 0 && !$isActive) {
        $_SESSION['error'] = "Your account is inactive. Please contact admin at " . SYSTEM_ADMIN_EMAIL . " to rectify this situation";
        header('location: index.php');
        exit;
    }

    $_SESSION['error'] = 'Email was not found';
    header('location: index.php');
    exit;

} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    $_SESSION['error'] = 'There was an error processing your request. Please try again later.';
    header('location: index.php');
    exit;
} finally {
    /**
     * Close database connection (if your Database wrapper supports it).
     * This runs even when exiting early inside the try/catch blocks.
     */
    if (isset($pdo)) {
        $pdo->close();
    }
}
