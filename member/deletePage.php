<?php
/**
 * deletePage.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Delete a capture page record by pageID (scoped to current user)
 * - Redirect back to the pages list page with a flash message
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
// Input: pageID (sanitized)
// -----------------------------------------------------------------------------
$pageID = filter_input(INPUT_GET, 'pageID', FILTER_SANITIZE_STRING);
$pageID = is_string($pageID) ? trim($pageID) : '';

if ($pageID === '') {
    $_SESSION['error'] = 'Missing Page ID.';
    header('Location: view-pages');
    exit;
}

try {
    $conn = $pdo->open();

    // IMPORTANT: Scope delete to the authenticated user to prevent IDOR
    $deleteStmt = $conn->prepare("
        DELETE FROM `pages`
        WHERE `pageID` = :pageID
          AND `userID` = :userID
        LIMIT 1
    ");
    $deleteStmt->execute([
        'pageID' => $pageID,
        'userID' => (int)$user['userID'],
    ]);

    if ($deleteStmt->rowCount() > 0) {
        $_SESSION['success'] = 'Perfect! Your capture page was successfully deleted!';
    } else {
        $_SESSION['error'] = 'Page not found or you do not have permission to delete it.';
    }
} catch (PDOException $ex) {
    error_log('deletePage.php error (pageID=' . $pageID . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while deleting the page.';
} finally {
    $pdo->close();
}

header('Location: view-pages');
exit;
