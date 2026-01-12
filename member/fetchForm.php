<?php
require 'includes/session.php';

if (isset($_GET['formID'])) {
    $formID = $_GET['formID'];

    $conn = $pdo->open();
    try {
        $formSelectedStmt = $conn->prepare("SELECT * FROM `form` WHERE `formID` = :formID");
        $formSelectedStmt->execute(['formID' => $formID]);
        $form = $formSelectedStmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($form);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    $pdo->close();
} else {
    echo json_encode(['error' => 'No formID provided']);
}
?>
