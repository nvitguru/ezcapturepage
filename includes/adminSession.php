<?php
session_start();

// Include database connection details
require_once 'conn.php';

// Check if the admin session is set
if (isset($_SESSION['admin'])) {
    $pdo = new Database();

    try {
        $conn = $pdo->open();

        // Fetch admin details
        $stmt = $conn->prepare("SELECT * FROM hmfic WHERE id=:id");
        $stmt->execute(['id' => $_SESSION['admin']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch admin data into $admin

        // Fetch message count
        $stmtMessages = $conn->prepare("SELECT COUNT(*) AS numrows FROM `notifications`");
        $stmtMessages->execute();
        $row = $stmtMessages->fetch();
        $notifyCount = $row['numrows'];

        $notifyStmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM `notifications` WHERE `active` = TRUE");
        $notifyStmt->execute(); // Ensure to execute the statement
        $alerts = $notifyStmt->fetch();
        $notifyAlertCount = $alerts['numrows'];

    } catch (PDOException $e) {
        echo "There is some problem in connection: " . $e->getMessage();
        // Optionally close connection or handle the error further
    }

    if ($admin) { // Ensure $admin is not empty
        $admin['level'] >= 99 ? ($callerView = "d-none") : ($callerView = "");
        $admin['level'] >= 99 ? ($callerHide = "") : ($callerHide = "d-none");
        $admin['level'] >= 100 ? ($adminHide = "") : ($adminHide = "d-none");
    } else {
        // Handle case where admin data is not fetched properly
        echo "Failed to retrieve admin details.";
    }
}


