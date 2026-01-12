<?php
/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

$conn = $pdo->open();

if(isset($_GET['formID'])){

    try{
        $deleteStmt = $conn->prepare("DELETE FROM `form` WHERE `formID` = :formID");
        $deleteStmt->execute(['formID'=> $_GET['formID']]);

        $_SESSION['success'] = 'Perfect! Your capture form was successfully deleted!';
    }
    catch(PDOException $e){
        $_SESSION['success'] = $e->getMessage();
    }

}

$pdo->close();

header("Location: view-forms");