<?php
/**
 * deleteCRM.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Delete a CRM record by crmID (scoped to current user)
 * - Redirect back to the CRM list page with a flash message
 *
 * Security Notes:
 * - This endpoint currently accepts GET to preserve existing behavior.
 * - For best practice, delete should be POST + CSRF (we can upgrade later).
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
// Input: crmID (sanitized)
// -----------------------------------------------------------------------------
$crmID = filter_input(INPUT_GET, 'crmID', FILTER_SANITIZE_STRING);
$crmID = is_string($crmID) ? trim($crmID) : '';

if ($crmID === '') {
    $_SESSION['error'] = 'Missing CRM ID.';
    header('Location: view-crms');
    exit;
}

try {
    $conn = $pdo->open();

    // IMPORTANT: Scope delete to the authenticated user to prevent IDOR
    $deleteStmt = $conn->prepare("
        DELETE FROM `crm`
        WHERE `crmID` = :crmID
          AND `userID` = :userID
        LIMIT 1
    ");
    $deleteStmt->execute([
        'crmID'  => $crmID,
        'userID' => (int)$user['userID'],
    ]);

    // rowCount() tells us whether a row was actually deleted
    if ($deleteStmt->rowCount() > 0) {
        $_SESSION['success'] = 'Perfect! Your CRM was successfully deleted!';
    } else {
        // Either not found, not owned by user, or already deleted
        $_SESSION['error'] = 'CRM not found or you do not have permission to delete it.';
    }
} catch (PDOException $ex) {
    // Log internal details; do not leak DB errors to users
    error_log('deleteCRM.php error (crmID=' . $crmID . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while deleting the CRM.';
} finally {
    $pdo->close();
}

header('Location: view-crms');
exit;
