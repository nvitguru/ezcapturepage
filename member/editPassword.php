<?php
/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

$conn = $pdo->open();

if (isset($_POST['editPassword'])) {
    $currentPass = filter_input(INPUT_POST, 'currentPass', FILTER_SANITIZE_STRING);
    $newPass = filter_input(INPUT_POST, 'newPass', FILTER_SANITIZE_STRING);
    $confirmPass = filter_input(INPUT_POST, 'confirmPass', FILTER_SANITIZE_STRING);

    if ($newPass !== $confirmPass) {
        $_SESSION['error'] = 'Oops! Your passwords did not match. Please try again.';
        header("Location: password");
        exit();
    }

    $conn->beginTransaction();

    try {

        if (!password_verify($currentPass, $user['password'])) {
            $_SESSION['error'] = 'Oops! You have entered the wrong current password. Please try again.';
            header("Location: password");
            exit();
        }

        $passNew = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=:password WHERE userID=:userID");
        $stmt->execute(['password' => $passNew, 'userID' => $user['userID']]);

        $conn->commit();  // Commit transaction
        $_SESSION['success'] = 'Your password was successfully changed!';
    } catch (PDOException $e) {
        $conn->rollBack();  // Rollback transaction on error
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    header("Location: password");
    exit();
}
