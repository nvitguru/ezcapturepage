<?php
/**
 * toggleCRM.php
 *
 * Responsibilities:
 * - Toggle a CRM record active/inactive for the current user context
 * - Set a flash message and redirect back to the CRM list
 *
 * Notes:
 * - This handler expects a POST request with `toggleCRM` and `crmID`.
 */

session_start();

include '../includes/session.php';
include '../includes/settings.php';

/**
 * Auth gate: only authenticated users should be able to toggle CRM status.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Guard clause: only accept valid POST submissions.
 */
if (!isset($_POST['toggleCRM'])) {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header('Location: dashboard.php');
    exit;
}

/**
 * Validate input.
 */
if (!isset($_POST['crmID']) || $_POST['crmID'] === '') {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header('Location: view-crms.php');
    exit;
}

$crmID = (int)$_POST['crmID'];

/**
 * Open database connection.
 */
try {
    $conn = $pdo->open();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database connection failed: ' . $e->getMessage();
    header('Location: dashboard.php');
    exit;
}

/**
 * Toggle active flag.
 */
$conn->beginTransaction();

try {
    $updateStmt = $conn->prepare("UPDATE crm SET active = !active WHERE crmID = :crmID");
    $updateStmt->execute(['crmID' => $crmID]);

    $conn->commit();

    $_SESSION['success'] = 'Capture Page CRM toggled successfully!';
    header('Location: view-crms.php');
    exit;

} catch (PDOException $e) {
    $conn->rollBack();

    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: view-crms.php');
    exit;
}
