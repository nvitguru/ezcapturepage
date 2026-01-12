<?php
session_start();

include '../includes/session.php';
include '../includes/settings.php';

try {
    $conn = $pdo->open();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database connection failed: ' . $e->getMessage();
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['levelID']) && isset($user['userID'])) {
    $userID = $user['userID'];
    $levelID = (int)$_GET['levelID'];

    $query = "
    SELECT 
        (SELECT COUNT(*) FROM pages WHERE `userID` = :userID) AS pages,
        (SELECT COUNT(*) FROM form WHERE `userID` = :userID) AS forms,
        (SELECT COUNT(*) FROM crm WHERE `userID` = :userID) AS crms
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute(['userID' => $userID]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $pageTotal = (int)$result['pages'];
    $formTotal = (int)$result['forms'];
    $crmTotal = (int)$result['crms'];

    if ($levelID == 2) {
        $newCount = 6;
    } elseif ($levelID == 3) {
        $newCount = 10;
    } else {
        $newCount = 3;
    }

    if ($newCount < $pageTotal || $newCount < $formTotal || $newCount < $crmTotal) {
        $_SESSION['warning'] = 'Before downgrading your account, make sure to only have ' . $newCount . ' pages, ' . $newCount . ' forms, and ' . $newCount . ' crms in your account.';
        header("Location: account");
        exit();
    } else {
        header("Location: checkout?levelID=" . $levelID);
        exit();
    }
} else {
    $_SESSION['error'] = 'Oops! An error occurred. Please try again later.';
    header("Location: dashboard");
    exit();
}
