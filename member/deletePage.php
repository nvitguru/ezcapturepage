<?php
/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

$conn = $pdo->open();

if(isset($_GET['pageID'])){

    try{
        $deleteStmt = $conn->prepare("DELETE FROM `pages` WHERE `pageID` = :pageID");
        $deleteStmt->execute(['pageID'=> $_GET['pageID']]);

        $_SESSION['success'] = 'Perfect! Your capture page was successfully deleted!';
    }
    catch(PDOException $e){
        $_SESSION['success'] = $e->getMessage();
    }

}

$pdo->close();

header("Location: view-pages");