<?php
/**
 * toggleDashPage.php
 *
 * Responsibilities:
 * - Toggle a capture page active/inactive state from the dashboard
 * - Set flash messaging and redirect back to the dashboard
 *
 * Notes:
 * - This handler expects a GET request with `pageID`
 */

session_start();

include '../includes/session.php';
include '../includes/settings.php';

/**
 * Auth gate: ensure only logged-in users can toggle pages.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Guard clause: validate required input.
 */
if (!isset($_GET['pageID']) || $_GET['pageID'] === '') {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header('Location: dashboard');
    exit;
}

$pageID = (int)$_GET['pageID'];

/**
 * Open database connection.
 */
try {
    $conn = $pdo->open();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database connection failed: ' . $e->getMessage();
    header('Location: dashboard');
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
    header('Location: dashboard');
    exit;

} catch (PDOException $e) {
    $conn->rollBack();

    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: dashboard');
    exit;
}
