<?php
/**
 * togglePage.php
 *
 * Responsibilities:
 * - Toggle a capture page active/inactive state from the pages list
 * - Set flash messaging and redirect back to the pages view
 *
 * Notes:
 * - This handler expects a POST request with `togglePage` and `pageID`
 */

session_start();

include '../includes/session.php';
include '../includes/settings.php';

/**
 * Auth gate: ensure only authenticated users can toggle pages.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Guard clause: validate request intent.
 */
if (!isset($_POST['togglePage'])) {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header('Location: dashboard.php');
    exit;
}

/**
 * Validate required input.
 */
if (!isset($_POST['pageID']) || $_POST['pageID'] === '') {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header('Location: view-pages.php');
    exit;
}

$pageID = (int)$_POST['pageID'];

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
 * Toggle page active status.
 */
$conn->beginTransaction();

try {
    $updateStmt = $conn->prepare(
        "UPDATE pages SET active = !active WHERE pageID = :pageID"
    );
    $updateStmt->execute(['pageID' => $pageID]);

    $conn->commit();

    $_SESSION['success'] = 'Capture Page toggled successfully!';
    header('Location: view-pages.php');
    exit;

} catch (PDOException $e) {
    $conn->rollBack();

    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: view-pages.php');
    exit;
}
