<?php
/**
 * createForm.php
 *
 * Responsibilities:
 * - Create a new Form record for the authenticated user (if membership allows)
 * - Generate a unique formID
 * - Redirect to the form page on success
 *
 * Notes:
 * - Relies on included files to hydrate: $pdo, $user, $activeForms, $pageCount
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['createForm'])) {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Enforce plan limits (as per existing behavior)
// -----------------------------------------------------------------------------
if ($activeForms >= $pageCount) {
    $_SESSION['error'] = 'Oops! You have reached the maximum amount of Forms for your membership level.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('DB connection failed in createForm.php: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Generate unique formID
// - 6 random bytes => 12 hex chars
// - Loop until not found in DB
// -----------------------------------------------------------------------------
$formID = '';
$maxAttempts = 25; // safety cap
$attempt = 0;

try {
    $existsStmt = $conn->prepare("SELECT COUNT(id) AS exist FROM form WHERE formID = :formID");

    do {
        $attempt++;

        $bytes  = random_bytes(6);
        $formID = bin2hex($bytes);

        $existsStmt->execute(['formID' => $formID]);
        $result = $existsStmt->fetch();

        $exists = isset($result['exist']) ? (int)$result['exist'] : 0;

        if ($attempt >= $maxAttempts) {
            throw new RuntimeException('Unable to generate a unique Form ID.');
        }
    } while ($exists > 0);

    // -------------------------------------------------------------------------
    // Insert Form record
    // -------------------------------------------------------------------------
    $userId = isset($user['userID']) ? (int)$user['userID'] : 0;

    $insertStmt = $conn->prepare("INSERT INTO form (userID, formID) VALUES (:userID, :formID)");
    $insertStmt->execute([
        'userID' => $userId,
        'formID' => $formID,
    ]);

    $_SESSION['success'] = 'Perfect! New Form has been created';
    $pdo->close();
    $redirect('form?formID=' . rawurlencode($formID));
} catch (Throwable $ex) {
    error_log('createForm.php error: ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while creating your form.';
    $pdo->close();
    $redirect('dashboard');
}
