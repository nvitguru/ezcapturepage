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

if (isset($_POST['toggleCRM'])) {

    $crmID = $_POST['crmID'];

    $conn->beginTransaction();

    try {
        $updateStmt = $conn->prepare("UPDATE crm SET active = !active WHERE crmID = :crmID");
        $updateStmt->execute(['crmID' => $crmID]);

        // Commit the transaction
        $conn->commit();
        $_SESSION['success'] = 'Capture Page CRM toggled successfully!';
        header("Location: view-crms.php");
    } catch (PDOException $e) {
        // Rollback the transaction in case of any error
        $conn->rollBack();
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header("Location: view-crms.php");
    }
} else    {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header("Location: dashboard.php");
}

exit();
