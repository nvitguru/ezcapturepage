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
    header("location: dashboard.php");
    exit();
}

if (isset($_GET['pageID'])) {

    $pageID = $_GET['pageID'];

    $conn->beginTransaction();

    try {
        $updateStmt = $conn->prepare("UPDATE pages SET active = !active WHERE pageID = :pageID");
        $updateStmt->execute(['pageID' => $pageID]);

        // Commit the transaction
        $conn->commit();
        $_SESSION['success'] = 'Capture Page toggled successfully!';
        header("Location: dashboard");
    } catch (PDOException $e) {
        // Rollback the transaction in case of any error
        $conn->rollBack();
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header("Location: dashboard");
    }
} else {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header("Location: dashboard");
}

exit();
?>
