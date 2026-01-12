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

if (isset($_POST['editProfile'])) {

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];

    $conn->beginTransaction();

    try {
        $updateStmt = $conn->prepare("UPDATE users SET fname = :fname, lname = :lname, email = :email WHERE userID = :userID");
        $updateStmt->execute(['fname' => $fname, 'lname' => $lname, 'email' => $email, 'userID' => $user['userID']]);

        // Commit the transaction
        $conn->commit();
        $_SESSION['success'] = 'Member profile has ben updated successfully!';
        header("Location: profile");
    } catch (PDOException $e) {
        // Rollback the transaction in case of any error
        $conn->rollBack();
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header("Location: profile");
    }
} else {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header("Location: profile");
}

exit();
