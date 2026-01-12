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

if (isset($_POST['editForm'])) {

    $formID = $_POST['formID'];
    $name = $_POST['name'];
    $crm = $_POST['crm'];
    $formName = $_POST['formName'];
    $formPhone = isset($_POST['formPhone']) ? true : false;
    $formHuman = isset($_POST['formHuman']) ? true : false;

    $conn->beginTransaction();

    try {
        $updateStmt = $conn->prepare("UPDATE form SET name = :name, crm = :crm, formName = :formName, formPhone = :formPhone, formHuman = :formHuman WHERE formID = :formID");
        $updateStmt->execute(['name' => $name, 'crm' => $crm, 'formName' => $formName, 'formPhone' => $formPhone, 'formHuman' => $formHuman, 'formID' => $formID]);

        // Commit the transaction
        $conn->commit();
        $_SESSION['success'] = 'Capture Page Form saved successfully!';
        header("Location: form?formID=" . $formID);
    } catch (PDOException $e) {
        // Rollback the transaction in case of any error
        $conn->rollBack();
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header("Location: form?formID=" . $formID);
    }
} else {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header("Location: dashboard");
}

exit();
?>
