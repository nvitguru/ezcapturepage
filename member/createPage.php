<?php
/**
 * createPage.php
 *
 * Responsibilities:
 * - Create a new capture page for the authenticated user (if membership allows)
 * - Generate a unique pageID
 * - Create related rows in content + metrics
 * - Redirect to the page editor on success
 *
 * Notes:
 * - Relies on included files to hydrate: $pdo, $user, $activePages, $pageCount
 */

/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Small redirect helper to guarantee consistent exits
// -----------------------------------------------------------------------------
$redirect = static function (string $to): void {
    header('Location: ' . $to);
    exit;
};

// -----------------------------------------------------------------------------
// Auth gate
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user'])) {
    $redirect('index.php');
}

// -----------------------------------------------------------------------------
// Must be a POST from the expected form button
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['createPage'])) {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Enforce plan limits
// -----------------------------------------------------------------------------
if ($activePages >= $pageCount) {
    $_SESSION['error'] = 'Oops! You have reached the maximum amount of Pages for your membership level.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('DB connection failed in createPage.php: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Generate unique pageID (12 hex chars) with prepared existence check
// -----------------------------------------------------------------------------
$pageID = '';
$maxAttempts = 25;
$attempt = 0;

try {
    $existsStmt = $conn->prepare("SELECT COUNT(id) AS exist FROM pages WHERE pageID = :pageID");

    do {
        $attempt++;

        $bytes  = random_bytes(6);
        $pageID = bin2hex($bytes);

        $existsStmt->execute(['pageID' => $pageID]);
        $row = $existsStmt->fetch();

        $exists = isset($row['exist']) ? (int)$row['exist'] : 0;

        if ($attempt >= $maxAttempts) {
            throw new RuntimeException('Unable to generate a unique Page ID.');
        }
    } while ($exists > 0);

    // -------------------------------------------------------------------------
    // Atomic creation: pages + content + metrics
    // -----------------------------------------------------------------------------
    $userId = isset($user['userID']) ? (int)$user['userID'] : 0;

    $conn->beginTransaction();

    // Create Page
    $pageStmt = $conn->prepare("INSERT INTO pages (userID, pageID) VALUES (:userID, :pageID)");
    $pageStmt->execute([
        'userID' => $userId,
        'pageID' => $pageID,
    ]);

    // Create Content
    $contentStmt = $conn->prepare("INSERT INTO content (pageID) VALUES (:pageID)");
    $contentStmt->execute(['pageID' => $pageID]);

    // Create Metrics
    $metricsStmt = $conn->prepare("INSERT INTO metrics (pageID) VALUES (:pageID)");
    $metricsStmt->execute(['pageID' => $pageID]);

    $conn->commit();

    $_SESSION['success'] = 'Perfect! New Capture Page has been created';
    $pdo->close();
    $redirect('page?pageID=' . rawurlencode($pageID));
} catch (Throwable $ex) {
    // Rollback if we started the transaction
    if (isset($conn) && $conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log('createPage.php error: ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while creating your capture page.';
    $pdo->close();
    $redirect('dashboard');
}
