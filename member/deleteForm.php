<?php
/**
 * deleteForm.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Delete a Form record by formID (scoped to current user)
 * - Redirect back to the forms list page with a flash message
 *
 * Security Notes:
 * - This endpoint currently accepts GET to preserve existing behavior.
 * - Best practice is POST + CSRF (upgrade later if/when you want).
 */

/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Auth gate
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// -----------------------------------------------------------------------------
// Input: formID (sanitized)
// -----------------------------------------------------------------------------
$formID = filter_input(INPUT_GET, 'formID', FILTER_SANITIZE_STRING);
$formID = is_string($formID) ? trim($formID) : '';

if ($formID === '') {
    $_SESSION['error'] = 'Missing Form ID.';
    header('Location: view-forms');
    exit;
}

try {
    $conn = $pdo->open();

    // IMPORTANT: Scope delete to the authenticated user to prevent IDOR
    $deleteStmt = $conn->prepare("
        DELETE FROM `form`
        WHERE `formID` = :formID
          AND `userID` = :userID
        LIMIT 1
    ");
    $deleteStmt->execute([
        'formID'  => $formID,
        'userID'  => (int)$user['userID'],
    ]);

    if ($deleteStmt->rowCount() > 0) {
        $_SESSION['success'] = 'Perfect! Your capture form was successfully deleted!';
    } else {
        $_SESSION['error'] = 'Form not found or you do not have permission to delete it.';
    }
} catch (PDOException $ex) {
    error_log('deleteForm.php error (formID=' . $formID . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while deleting the form.';
} finally {
    $pdo->close();
}

header('Location: view-forms');
exit;
