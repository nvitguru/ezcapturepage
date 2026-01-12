<?php
/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

try {
    $conn = $pdo->open();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database connection failed: ' . $e->getMessage();
    header("location: dashboard");
    exit();
}

if(isset($_POST['createCRM'])){
    if($activeCRMs >= $pageCount){
        $_SESSION['error'] = 'Oops! You have reached the maximum amount of CRMs for your membership level.';
        header("location: dashboard");
        exit();
    }

    do {
        $bytes = random_bytes(6);
        $crmID = bin2hex($bytes);

        $stmt = $conn->prepare("SELECT COUNT(id) AS exist FROM crm WHERE crmID = :crmID");
        $stmt->execute(['crmID' => $crmID]);
        $result = $stmt->fetch();
    } while ($result['exist'] > 0);

    try {
        $crmStmt = $conn->prepare("INSERT INTO crm (userID, crmID) VALUES (?, ?)");
        $crmStmt->execute([$user['userID'], $crmID]);

        $_SESSION['success'] = 'Perfect! New CRM has been created';
        header("location: crm?crmID=" . $crmID);
        exit();
    } catch (PDOException $e) {
        error_log("PDO Exception: " . $e->getMessage(), 0);
        $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
        $pdo->close();
        header("location: dashboard");
        exit();
    }
} else {
    $_SESSION['error'] = 'Please enter item information!';
    $pdo->close();
    header("location: dashboard");
    exit();
}

