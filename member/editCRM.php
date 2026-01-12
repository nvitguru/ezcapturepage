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

if (isset($_POST['editSystemCRM'])) {

    $crmID = $_POST['crmID'];

    $conn->beginTransaction();

    try {
        $updateStmt = $conn->prepare("UPDATE crm SET name = :name, crmType = :crmType, crmCode = :crmCode, crmRedirect = :crmRedirect WHERE crmID = :crmID");
        $updateStmt->execute(['name' => "Internal CRM", 'crmType' => 0, 'crmCode' => NULL, 'crmRedirect' => NULL, 'crmID' => $crmID]);

        // Commit the transaction
        $conn->commit();
        $_SESSION['success'] = 'Capture Page CRM Integration saved successfully!';
        header("Location: crm?crmID=" . $crmID);
    } catch (PDOException $e) {
        // Rollback the transaction in case of any error
        $conn->rollBack();
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header("Location: crm?crmID=" . $crmID);
    }
} elseif (isset($_POST['editAweberCRM'])) {

    $crmID = $_POST['crmID'];
    $name = $_POST['name'];
    $crmCode = $_POST['crmCode'];
    $crmRedirect = $_POST['crmRedirect'];

    $conn->beginTransaction();

    try {
        $updateStmt = $conn->prepare("UPDATE crm SET name = :name, crmType = :crmType, crmCode = :crmCode, crmRedirect = :crmRedirect WHERE crmID = :crmID");
        $updateStmt->execute(['name' => $name, 'crmType' => 1, 'crmCode' => $crmCode, 'crmRedirect' => $crmRedirect, 'crmID' => $crmID]);

        // Commit the transaction
        $conn->commit();
        $_SESSION['success'] = 'Capture Page CRM Integration saved successfully!';
        header("Location: crm?crmID=" . $crmID);
    } catch (PDOException $e) {
        // Rollback the transaction in case of any error
        $conn->rollBack();
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header("Location: crm?crmID=" . $crmID);
    }
} elseif (isset($_POST['editResponseCRM'])) {

    $crmID = $_POST['crmID'];
    $name = $_POST['name'];
    $crmCode = $_POST['crmCode'];
    $crmRedirect = $_POST['crmRedirect'];

    $conn->beginTransaction();

    try {
        $updateStmt = $conn->prepare("UPDATE crm SET name = :name, crmType = :crmType, crmCode = :crmCode, crmRedirect = :crmRedirect WHERE crmID = :crmID");
        $updateStmt->execute(['name' => $name, 'crmType' => 2, 'crmCode' => $crmCode, 'crmRedirect' => $crmRedirect, 'crmID' => $crmID]);

        // Commit the transaction
        $conn->commit();
        $_SESSION['success'] = 'Capture Page CRM Integration saved successfully!';
        header("Location: crm?crmID=" . $crmID);
    } catch (PDOException $e) {
        // Rollback the transaction in case of any error
        $conn->rollBack();
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header("Location: crm?crmID=" . $crmID);
    }
} else    {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header("Location: dashboard");
}

exit();
