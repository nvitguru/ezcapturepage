<?php
/**
 * toggleForm.php
 *
 * Responsibilities:
 * - Toggle a capture form active/inactive state
 * - Set flash messaging and redirect back to the forms list
 *
 * Notes:
 * - This handler expects a POST request with `toggleForm` and `formID`
 */

session_start();

include '../includes/session.php';
include '../includes/settings.php';

/**
 * Auth gate: ensure only authenticated users can toggle forms.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Guard clause: validate request intent.
 */
if (!isset($_POST['toggleForm'])) {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header('Location: dashboard.php');
    exit;
}

/**
 * Validate required input.
 */
if (!isset($_POST['formID']) || $_POST['formID'] === '') {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header('Location: view-forms.php');
    exit;
}

$formID = (int)$_POST['formID'];

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
 * Toggle form active status.
 */
$conn->beginTransaction();

try {
    $updateStmt = $conn->prepare(
        "UPDATE form SET active = !active WHERE formID = :formID"
    );
    $updateStmt->execute(['formID' => $formID]);

    $conn->commit();

    $_SESSION['success'] = 'Capture Page Form toggled successfully!';
    header('Location: view-forms.php');
    exit;

} catch (PDOException $e) {
    $conn->rollBack();

    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: view-forms.php');
    exit;
}
