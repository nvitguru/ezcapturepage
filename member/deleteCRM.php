<?php
/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

$conn = $pdo->open();

if(isset($_GET['crmID'])){

    try{
        $deleteStmt = $conn->prepare("DELETE FROM `crm` WHERE `crmID` = :crmID");
        $deleteStmt->execute(['crmID'=> $_GET['crmID']]);

        $_SESSION['success'] = 'Perfect! Your CRM was successfully deleted!';
    }
    catch(PDOException $e){
        $_SESSION['success'] = $e->getMessage();
    }

}

$pdo->close();

header("Location: view-crms");