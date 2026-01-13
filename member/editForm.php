<?php
/**
 * editForm.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Update a Form record owned by the authenticated user
 * - Redirect back to the form editor with a flash message
 *
 * Notes:
 * - Relies on includes to hydrate: $pdo, $user
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
// POST-only + expected submit key
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['editForm'])) {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Input: formID (required)
// -----------------------------------------------------------------------------
$formID = isset($_POST['formID']) ? trim((string)$_POST['formID']) : '';
if ($formID === '') {
    $_SESSION['error'] = 'Missing Form ID.';
    $redirect('dashboard');
}

$userID = (int)$user['userID'];

// -----------------------------------------------------------------------------
// Input normalization (keeps behavior but avoids notices)
// -----------------------------------------------------------------------------
$name     = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
$crm      = isset($_POST['crm']) ? trim((string)$_POST['crm']) : '';
$formName = isset($_POST['formName']) ? trim((string)$_POST['formName']) : '';

// Checkbox flags: store as 1/0 (safer for DB and consistent across PDO drivers)
$formPhone = isset($_POST['formPhone']) ? 1 : 0;
$formHuman = isset($_POST['formHuman']) ? 1 : 0;

// Optional: store empty strings as NULL (cleaner DB semantics; does not affect UI)
$name     = ($name === '') ? null : $name;
$crm      = ($crm === '') ? null : $crm;
$formName = ($formName === '') ? null : $formName;

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('editForm.php DB connect error: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Update (scoped by formID + userID to prevent IDOR)
// -----------------------------------------------------------------------------
try {
    $conn->beginTransaction();

    $updateStmt = $conn->prepare("
        UPDATE form
        SET
            name = :name,
            crm = :crm,
            formName = :formName,
            formPhone = :formPhone,
            formHuman = :formHuman
        WHERE formID = :formID
          AND userID = :userID
        LIMIT 1
    ");

    $updateStmt->execute([
        'name'      => $name,
        'crm'       => $crm,
        'formName'  => $formName,
        'formPhone' => $formPhone,
        'formHuman' => $formHuman,
        'formID'    => $formID,
        'userID'    => $userID,
    ]);

    if ($updateStmt->rowCount() < 1) {
        $conn->rollBack();
        $_SESSION['error'] = 'Form not found or you do not have permission to edit it.';
        $pdo->close();
        $redirect('dashboard');
    }

    $conn->commit();
    $_SESSION['success'] = 'Capture Page Form saved successfully!';
    $pdo->close();
    $redirect('form?formID=' . rawurlencode($formID));
} catch (PDOException $ex) {
    if ($conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log('editForm.php update error (formID=' . $formID . ', userID=' . $userID . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'An error occurred while saving your form.';
    $pdo->close();
    $redirect('form?formID=' . rawurlencode($formID));
}
