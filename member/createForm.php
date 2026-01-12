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

if(isset($_POST['createForm'])){

    if($activeForms >= $pageCount){
        $_SESSION['error'] = 'Oops! You have reached the maximum amount of Forms for your membership level.';
        header("location: dashboard");
        exit();
    }

    do {
        $bytes = random_bytes(6);
        $formID = bin2hex($bytes);

        $stmt = $conn->prepare("SELECT COUNT(id) AS exist FROM form WHERE formID = :formID");
        $stmt->execute(['formID' => $formID]);
        $result = $stmt->fetch();
    } while ($result['exist'] > 0);

    try {
        $formStmt = $conn->prepare("INSERT INTO form (userID, formID) VALUES (?, ?)");
        $formStmt->execute([$user['userID'], $formID]);

        $_SESSION['success'] = 'Perfect! New Form has been created';
        header("location: form?formID=" . $formID);
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

