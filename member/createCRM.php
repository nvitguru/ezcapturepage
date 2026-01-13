<?php
/**
 * createCRM.php
 *
 * Responsibilities:
 * - Create a new CRM record for the authenticated user (if membership allows)
 * - Generate a unique crmID
 * - Redirect to the CRM page on success
 *
 * Notes:
 * - Relies on included files to hydrate: $pdo, $user, $activeCRMs, $pageCount
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
// Auth gate (consistent with other member endpoints)
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user'])) {
    $redirect('index.php');
}

// -----------------------------------------------------------------------------
// Must be a POST from the expected form button
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['createCRM'])) {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Enforce plan limits (as per existing behavior)
// -----------------------------------------------------------------------------
if ($activeCRMs >= $pageCount) {
    $_SESSION['error'] = 'Oops! You have reached the maximum amount of CRMs for your membership level.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    // Log internal details, present generic error to user
    error_log('DB connection failed in createCRM.php: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Generate unique crmID
// - 6 random bytes => 12 hex chars
// - Loop until not found in DB
// -----------------------------------------------------------------------------
$crmID = '';
$maxAttempts = 25; // safety cap (should never hit under normal conditions)
$attempt = 0;

try {
    $existsStmt = $conn->prepare("SELECT COUNT(id) AS exist FROM crm WHERE crmID = :crmID");

    do {
        $attempt++;

        $bytes = random_bytes(6);
        $crmID = bin2hex($bytes);

        $existsStmt->execute(['crmID' => $crmID]);
        $result = $existsStmt->fetch();

        $exists = isset($result['exist']) ? (int)$result['exist'] : 0;

        if ($attempt >= $maxAttempts) {
            throw new RuntimeException('Unable to generate a unique CRM ID.');
        }
    } while ($exists > 0);

    // -------------------------------------------------------------------------
    // Insert CRM record
    // -------------------------------------------------------------------------
    $userId = isset($user['userID']) ? (int)$user['userID'] : 0;

    $insertStmt = $conn->prepare("INSERT INTO crm (userID, crmID) VALUES (:userID, :crmID)");
    $insertStmt->execute([
        'userID' => $userId,
        'crmID'  => $crmID,
    ]);

    $_SESSION['success'] = 'Perfect! New CRM has been created';
    $pdo->close();
    $redirect('crm?crmID=' . rawurlencode($crmID));
} catch (Throwable $ex) {
    // Log details, keep UI message generic
    error_log('createCRM.php error: ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while creating your CRM.';
    $pdo->close();
    $redirect('dashboard');
}
