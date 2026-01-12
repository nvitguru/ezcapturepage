<?php

/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

$conn = $pdo->open();

if(isset($_POST['createPage'])){

    if($activePages >= $pageCount){
        $_SESSION['error'] = 'Oops! You have reached the maximum amount of Pages for your membership level.';
        header("location: dashboard");
        exit();
    }

    do {
        // Generate a unique 12-character alphanumeric ID
        $bytes = random_bytes(6);
        $pageID = bin2hex($bytes);
        $result = $conn->query(sprintf("SELECT COUNT(id) AS exist FROM pages WHERE pageID = '%s';", $pageID));
    } while ($result->fetch()['exist'] > 0);


    try {
        // Create Page
        $pageStmt = $conn->prepare("INSERT INTO pages (userID, pageID) VALUES (?, ?)");
        $pageStmt->execute([$user['userID'], $pageID]);

        // Create Content
        $contentStmt = $conn->prepare("INSERT INTO content (pageID) VALUES (?)");
        $contentStmt->execute([$pageID]);

        // Create Metrics
        $metricsStmt = $conn->prepare("INSERT INTO metrics (pageID) VALUES (?)");
        $metricsStmt->execute([$pageID]);

        $_SESSION['success'] = 'Perfect! New Capture Page has been created';
        header("location: page?pageID=" . $pageID);

    } catch (PDOException $e) {
        error_log("PDO Exception: " . $e->getMessage(), 0);
        $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
        $pdo->close();
        header("location: dashboard");
    }

} else {
    $_SESSION['error'] = 'Please enter item information!';
    $pdo->close();
    header("location: dashboard");
}
?>