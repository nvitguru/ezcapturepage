<?php
/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

$conn = $pdo->open();

try {
    $conn = $pdo->open();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database connection failed: ' . $e->getMessage();
    header("location: dashboard");
    exit();
}

if (isset($_POST['editSecurity'])) {

    $pincode = $_POST['pincode'];
    $currentPass = $_POST['password'];

    // Validate pincode length
    if (preg_match('/^\d{6,10}$/', $pincode)) {
        $conn->beginTransaction();

        if (password_verify($currentPass, $user['password'])) {
            try {
                $updateStmt = $conn->prepare("UPDATE users SET pincode = :pincode WHERE userID = :userID");
                $updateStmt->execute(['pincode' => $pincode, 'userID' => $user['userID']]);

                // Commit the transaction
                $conn->commit();
                $_SESSION['success'] = 'Security pin code has been updated successfully!';
                header("Location: profile");
            } catch (PDOException $e) {
                // Rollback the transaction in case of any error
                $conn->rollBack();
                $_SESSION['error'] = 'Database error: ' . $e->getMessage();
                header("Location: profile");
            }
        } else {
            $_SESSION['error'] = 'Incorrect password';
        }
    } else {
        $_SESSION['error'] = 'Pin code must be between 6 and 10 digits long';
        header("Location: profile");
    }
} else {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header("Location: profile");
}

exit();
