<?php
/**
 * editPage.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Validate + persist capture page settings (pages + content tables)
 * - Redirect back to the page editor with a flash message
 *
 * Security:
 * - POST-only
 * - Scopes updates by pageID + userID (prevents IDOR)
 * - Uses transaction for multi-table update atomicity
 *
 * Notes:
 * - This file currently HTML-escapes some inputs before storing. While escaping is
 *   typically done at output time, this behavior is preserved to avoid breaking
 *   existing rendering expectations.
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['editPage'])) {
    $_SESSION['error'] = 'Invalid request.';
    $redirect('dashboard');
}

$userID = (int)$user['userID'];

// -----------------------------------------------------------------------------
// Input: pageID (required)
// -----------------------------------------------------------------------------
$pageID = isset($_POST['pageID']) ? trim((string)$_POST['pageID']) : '';
if ($pageID === '') {
    $_SESSION['error'] = 'Missing Page ID.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Input normalization (preserve existing behavior: store HTML-escaped strings)
// -----------------------------------------------------------------------------
$sanitizeText = static function ($val): string {
    return htmlspecialchars(trim((string)$val), ENT_QUOTES, 'UTF-8');
};

$name       = $sanitizeText($_POST['name'] ?? '');
$tabTitle   = $sanitizeText($_POST['tabTitle'] ?? '');
$google     = $sanitizeText($_POST['google'] ?? '');
$pixel      = $sanitizeText($_POST['pixel'] ?? '');
$color      = $sanitizeText($_POST['color'] ?? '');
$background = $sanitizeText($_POST['background'] ?? '');
$headerText = $sanitizeText($_POST['header'] ?? '');
$subheader  = $sanitizeText($_POST['subheader'] ?? '');
$disclaimer = $sanitizeText($_POST['disclaimer'] ?? '');

// form + button are required by your business rule
$formID = isset($_POST['form']) ? trim((string)$_POST['form']) : '';
$button = isset($_POST['button']) ? trim((string)$_POST['button']) : '';

// video: preserve your sentinel behavior ("99" when empty)
$video = isset($_POST['video']) ? trim((string)$_POST['video']) : '';
$video = ($video !== '') ? $video : '99';

if ($formID === '' || $button === '') {
    $_SESSION['error'] = 'You must select a form and a button to save your page.';
    $redirect('page?pageID=' . rawurlencode($pageID));
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('editPage.php DB connect error: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed. Please try again later.';
    $redirect('dashboard');
}

// -----------------------------------------------------------------------------
// Persist changes (scoped to page ownership)
// -----------------------------------------------------------------------------
try {
    $conn->beginTransaction();

    // Ensure the page belongs to the authenticated user (prevents IDOR)
    $ownsStmt = $conn->prepare("
        SELECT 1
        FROM pages
        WHERE pageID = :pageID
          AND userID = :userID
        LIMIT 1
    ");
    $ownsStmt->execute([
        'pageID' => $pageID,
        'userID' => $userID,
    ]);

    if (!$ownsStmt->fetchColumn()) {
        $conn->rollBack();
        $pdo->close();
        $_SESSION['error'] = 'Page not found or you do not have permission to edit it.';
        $redirect('dashboard');
    }

    // Update `content`
    $updateContent = $conn->prepare("
        UPDATE content
        SET
            tabTitle    = :tabTitle,
            color       = :color,
            background  = :background,
            header      = :header,
            subheader   = :subheader,
            video       = :video,
            formID      = :formID,
            button      = :button,
            disclaimer  = :disclaimer
        WHERE pageID = :pageID
        LIMIT 1
    ");
    $updateContent->execute([
        'tabTitle'    => $tabTitle,
        'color'       => $color,
        'background'  => $background,
        'header'      => $headerText,
        'subheader'   => $subheader,
        'video'       => $video,
        'formID'      => $formID,
        'button'      => $button,
        'disclaimer'  => $disclaimer,
        'pageID'      => $pageID,
    ]);

    // Update `pages` (scoped by userID)
    $updatePages = $conn->prepare("
        UPDATE pages
        SET
            name   = :name,
            google = :google,
            pixel  = :pixel
        WHERE pageID = :pageID
          AND userID = :userID
        LIMIT 1
    ");
    $updatePages->execute([
        'name'   => $name,
        'google' => $google,
        'pixel'  => $pixel,
        'pageID' => $pageID,
        'userID' => $userID,
    ]);

    if ($updatePages->rowCount() < 1) {
        // Shouldn't happen because of owns check, but safe guard.
        $conn->rollBack();
        $pdo->close();
        $_SESSION['error'] = 'Page not found or you do not have permission to edit it.';
        $redirect('dashboard');
    }

    $conn->commit();
    $pdo->close();

    $_SESSION['success'] = 'Capture Page saved successfully!';
    $redirect('page?pageID=' . rawurlencode($pageID));
} catch (PDOException $ex) {
    if ($conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log('editPage.php save error (pageID=' . $pageID . ', userID=' . $userID . '): ' . $ex->getMessage());
    $pdo->close();

    $_SESSION['error'] = 'An error occurred while saving your page. Please try again.';
    $redirect('page?pageID=' . rawurlencode($pageID));
}
