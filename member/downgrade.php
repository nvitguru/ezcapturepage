<?php
/**
 * downgrade.php
 *
 * Responsibilities:
 * - Validate requested target level
 * - Ensure user is eligible to downgrade based on current resource usage limits
 * - Redirect to account with a warning if over limit, otherwise send to checkout
 *
 * Notes:
 * - This file relies on includes to hydrate: $pdo, $user
 * - This endpoint uses GET to preserve existing navigation behavior.
 */

session_start();

include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Redirect helper (ensures exit every time)
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
// Input: levelID (validate + allowlist)
// -----------------------------------------------------------------------------
$levelID = filter_input(INPUT_GET, 'levelID', FILTER_VALIDATE_INT);
$levelID = ($levelID !== null && $levelID !== false) ? (int)$levelID : 0;

if (!in_array($levelID, [1, 2, 3], true)) {
    $_SESSION['error'] = 'Invalid plan selected.';
    $redirect('account');
}

$userID = (int)$user['userID'];

// -----------------------------------------------------------------------------
// Determine limits for the selected plan
// -----------------------------------------------------------------------------
$limitsByLevel = [
    1 => 3,
    2 => 6,
    3 => 10,
];

$newCount = $limitsByLevel[$levelID];

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('downgrade.php DB connect error: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed.';
    $redirect('dashboard');
}

try {
    // Count resources for this user
    $query = "
        SELECT 
            (SELECT COUNT(*) FROM pages WHERE `userID` = :userID) AS pages,
            (SELECT COUNT(*) FROM form  WHERE `userID` = :userID) AS forms,
            (SELECT COUNT(*) FROM crm   WHERE `userID` = :userID) AS crms
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute(['userID' => $userID]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $pageTotal = isset($result['pages']) ? (int)$result['pages'] : 0;
    $formTotal = isset($result['forms']) ? (int)$result['forms'] : 0;
    $crmTotal  = isset($result['crms'])  ? (int)$result['crms']  : 0;

    // If the user's current usage exceeds the new plan limit, block downgrade
    if ($newCount < $pageTotal || $newCount < $formTotal || $newCount < $crmTotal) {
        $_SESSION['warning'] =
            'Before downgrading your account, make sure to only have ' . $newCount .
            ' pages, ' . $newCount . ' forms, and ' . $newCount . ' crms in your account.';
        $pdo->close();
        $redirect('account');
    }

    // Eligible: continue to checkout for the selected plan
    $pdo->close();
    $redirect('checkout?levelID=' . rawurlencode((string)$levelID));
} catch (PDOException $ex) {
    error_log('downgrade.php query error (userID=' . $userID . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    $pdo->close();
    $redirect('dashboard');
}
