<?php
/**
 * editCRM.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Update CRM integration settings for a CRM owned by the authenticated user
 * - Supports three actions via submit button names:
 *   - editSystemCRM  (crmType=0; resets fields)
 *   - editAweberCRM  (crmType=1)
 *   - editResponseCRM (crmType=2)
 *
 * Security:
 * - POST-only
 * - Scopes update by crmID + userID (prevents IDOR)
 * - Does not leak DB errors to users
 */

/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Redirect helper
// -----------------------------------------------------------------------------
$redirect = static function (string $to): void {
    header('Location: ' . $to);
    exit;
};

// -----------------------------------------------------------------------------
// Auth gate
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user']) || empty($user['userID'])) {
    $redirect('index.php');
}

// -----------------------------------------------------------------------------
// POST-only
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Input: crmID (required)
// -----------------------------------------------------------------------------
$crmID = isset($_POST['crmID']) ? trim((string)$_POST['crmID']) : '';
if ($crmID === '') {
    $_SESSION['error'] = 'Missing CRM ID.';
    $redirect('dashboard');
}

$userID = (int)$user['userID'];

// -----------------------------------------------------------------------------
// Determine action + normalize payload
// -----------------------------------------------------------------------------
$action = null;
$crmType = null;

if (isset($_POST['editSystemCRM'])) {
    $action = 'system';
    $crmType = 0;
} elseif (isset($_POST['editAweberCRM'])) {
    $action = 'aweber';
    $crmType = 1;
} elseif (isset($_POST['editResponseCRM'])) {
    $action = 'response';
    $crmType = 2;
} else {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('dashboard');
}

// Basic input normalization (keeps existing behavior but avoids notices)
$name        = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
$crmCode     = isset($_POST['crmCode']) ? trim((string)$_POST['crmCode']) : '';
$crmRedirect = isset($_POST['crmRedirect']) ? trim((string)$_POST['crmRedirect']) : '';

// System CRM forces known values (matches your original intent)
if ($action === 'system') {
    $name = 'Internal CRM';
    $crmCode = null;
    $crmRedirect = null;
} else {
    // Non-system: allow empty strings but store as NULL when empty (cleaner DB semantics)
    $name = ($name === '') ? null : $name;
    $crmCode = ($crmCode === '') ? null : $crmCode;
    $crmRedirect = ($crmRedirect === '') ? null : $crmRedirect;
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('editCRM.php DB connect error: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Update (scoped by crmID + userID)
// -----------------------------------------------------------------------------
try {
    $conn->beginTransaction();

    $updateStmt = $conn->prepare("
        UPDATE crm
        SET
            name = :name,
            crmType = :crmType,
            crmCode = :crmCode,
            crmRedirect = :crmRedirect
        WHERE crmID = :crmID
          AND userID = :userID
        LIMIT 1
    ");

    $updateStmt->execute([
        'name'        => $name,
        'crmType'     => $crmType,
        'crmCode'     => $crmCode,
        'crmRedirect' => $crmRedirect,
        'crmID'       => $crmID,
        'userID'      => $userID,
    ]);

    // If no row updated, it's either not found or not owned by the user (IDOR protection)
    if ($updateStmt->rowCount() < 1) {
        $conn->rollBack();
        $_SESSION['error'] = 'CRM not found or you do not have permission to edit it.';
        $pdo->close();
        $redirect('dashboard');
    }

    $conn->commit();
    $_SESSION['success'] = 'Capture Page CRM Integration saved successfully!';
    $pdo->close();
    $redirect('crm?crmID=' . rawurlencode($crmID));
} catch (PDOException $ex) {
    if ($conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log('editCRM.php update error (crmID=' . $crmID . ', userID=' . $userID . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while saving your CRM settings.';
    $pdo->close();
    $redirect('crm?crmID=' . rawurlencode($crmID));
}
