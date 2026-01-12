<?php
session_start();

//Mobile Hide Feature
$isMobile = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));
$hideMobile = $isMobile ? "d-none" : "";
$showMobile = $isMobile ? "" : "d-none";

// Include database connection details
require_once 'conn.php';

// Check if the user session is set
if (isset($_SESSION['user'])) {
    $pdo = new Database();

    try {
        $conn = $pdo->open();

        // Fetch user details
        $stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->execute(['id' => $_SESSION['user']]);
        if ($user = $stmt->fetch()) {

            $pageStmt = $conn->prepare("SELECT * FROM `pages` WHERE `userID` = :userID");
            $pageStmt->execute(['userID' => $user['userID']]);
            $page = $pageStmt->fetch();

        }

        // Fetch message count
        $currentDate = new DateTime();
        $weekAgoDate = clone $currentDate; // Clone to avoid modifying the original date
        $weekAgoDate->sub(new DateInterval('P7D'));
        $weekAgoDateFormatted = $weekAgoDate->format('Y-m-d H:i:s');

        if($user['level'] == 2){
            $pageCount = 6;
            $levelDisplay = "Standard Member";
            $levelName = "Standard Plan";
            $levelCost = "$14.99";
        } elseif ($user['level'] == 3){
            $pageCount = 10;
            $levelDisplay = "Level 3";
            $levelName = "Pro Member";
            $levelCost = "$19.99";
        } else {
            $pageCount = 3;
            $levelDisplay = "Level 1";
            $levelName = "start-Up Member";
            $levelCost = "$9.99";
        }

        //Quick View
        $query = "
        SELECT 
            (SELECT COUNT(*) AS numrows FROM pages WHERE `userID` = :userID) AS pages,
            (SELECT COUNT(*) AS numrows FROM form WHERE `userID` = :userID) AS forms,
            (SELECT COUNT(*) AS numrows FROM crm WHERE userID = :userID) AS crms
        ";

        $countStmt = $conn->prepare($query);
        $countStmt->execute(['userID' => $user['userID']]);

        $result = $countStmt->fetch(PDO::FETCH_ASSOC);

        $activePages = $result['pages'];
        $activeForms = $result['forms'];
        $activeCRMs = $result['crms'];

        $notifyStmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM `notifications` WHERE `active` = TRUE");
        $notifyStmt->execute(); // Ensure to execute the statement
        $alerts = $notifyStmt->fetch();
        $notifyAlertCount = $alerts['numrows'];

        $stmtMessages = $conn->prepare("SELECT COUNT(*) AS numrows FROM `notifications` WHERE `active` = TRUE AND `created` >= :weekAgoDate");
        $stmtMessages->bindParam(':weekAgoDate', $weekAgoDateFormatted);
        $stmtMessages->execute(); // Ensure to execute the statement
        $row = $stmtMessages->fetch();
        $notifyCount = $row['numrows'];

    } catch (PDOException $e) {
        echo "There is some problem in connection: " . $e->getMessage();
        // Optionally close connection or handle the error further
    }
}

