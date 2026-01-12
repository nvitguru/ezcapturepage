<?php
session_start();
include '../includes/session.php';
include '../includes/settings.php';

// Ensure database connection is available
if (!isset($pdo)) {
    $_SESSION['error'] = 'Database connection not initialized.';
    header('location: index.php');
    exit;
}

$conn = $pdo->open();

// Handle the login request
if (isset($_POST['memberLogin'])) {
    $email = filter_input(INPUT_POST, 'loginEmail', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'loginPassword', FILTER_SANITIZE_STRING);

    if (!$email || !$password) {
        $_SESSION['error'] = 'Invalid email or password format.';
        header('location: index.php');
        exit;
    }

    try {
        // Check if the user is an admin in the 'users' table
        $adminStmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM hmfic WHERE email = :email");
        $adminStmt->execute(['email' => $email]);
        $admin = $adminStmt->fetch();

        if ($admin['numrows'] > 0) {
            $_SESSION['error'] = 'You are trying to access the wrong back office portal. Please <a href="https://' . SYSTEM_NAME . '/admin">CLICK HERE</a> to get to the admin portal.';
            header("location: index.php");
            exit;
        }

        // Check if the user with the provided email exists
        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user['numrows'] > 0 && $user['active']) {
            if (password_verify($password, $user['password'])) {

                // Successful login, set user session and redirect to dashboard.php
                $_SESSION['user'] = $user['id'];
                header('location: dashboard.php');
                exit;
            } else {
                $_SESSION['error'] = 'The password entered was incorrect';
                header('location: index.php');
                exit;
            }
        } else {
            $_SESSION['error'] = $user['active'] ? 'Email was not found' : "Your account is inactive. Please contact admin at " . SYSTEM_ADMIN_EMAIL . " to rectify this situation";
            header('location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error'] = 'There was an error processing your request. Please try again later.';
        header('location: index.php');
        exit;
    }
} else {
    $_SESSION['error'] = 'Input login information';
    header('location: index.php');
    exit;
}

// Close the database connection
$pdo->close();

