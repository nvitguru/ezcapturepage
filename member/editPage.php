<?php
/** @var Database $pdo */
session_start();

include '../includes/session.php';
include '../includes/settings.php';

$conn = $pdo->open();

try {
    $conn = $pdo->open();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database connection failed. Please try again later.';
    error_log("Database connection failed: " . $e->getMessage());
    header("Location: dashboard");
    exit();
}

if (isset($_POST['editPage'])) {
    // Sanitize and validate inputs
    $pageID = $_POST['pageID'];
    $name = htmlspecialchars($_POST['name']);
    $tabTitle = htmlspecialchars($_POST['tabTitle']);
    $google = htmlspecialchars($_POST['google']);
    $pixel = htmlspecialchars($_POST['pixel']);
    $color = htmlspecialchars($_POST['color']);
    $background = htmlspecialchars($_POST['background']);
    $header = htmlspecialchars($_POST['header']);
    $subheader = htmlspecialchars($_POST['subheader']);
    $formID = $_POST['form'] ?? null;
    $button = $_POST['button'] ?? null;
    $disclaimer = htmlspecialchars($_POST['disclaimer']);
    $video = isset($_POST['video']) && $_POST['video'] !== "" ? $_POST['video'] : "99";

    if (!$formID || !$button) {
        $_SESSION['error'] = 'You must select a form and a button to save your page.';
        header("Location: page?pageID=" . $pageID);
        exit();
    }

    try {
        $conn->beginTransaction();

        // Update `content` table
        $updateStmt = $conn->prepare("UPDATE content SET 
            tabTitle = :tabTitle, 
            color = :color, 
            background = :background, 
            header = :header, 
            subheader = :subheader, 
            video = :video, 
            formID = :formID, 
            button = :button, 
            disclaimer = :disclaimer 
            WHERE pageID = :pageID");
        $updateStmt->execute([
            'tabTitle' => $tabTitle,
            'color' => $color,
            'background' => $background,
            'header' => $header,
            'subheader' => $subheader,
            'video' => $video,
            'formID' => $formID,
            'button' => $button,
            'disclaimer' => $disclaimer,
            'pageID' => $pageID
        ]);

        // Update `pages` table
        $trackingStmt = $conn->prepare("UPDATE pages SET 
            name = :name, 
            google = :google, 
            pixel = :pixel 
            WHERE pageID = :pageID");
        $trackingStmt->execute([
            'name' => $name,
            'google' => $google,
            'pixel' => $pixel,
            'pageID' => $pageID
        ]);

        $conn->commit();
        $_SESSION['success'] = 'Capture Page saved successfully!';
        header("Location: page?pageID=" . $pageID);
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = 'An error occurred while saving your page. Please try again.';
        error_log("Database error: " . $e->getMessage());
        header("Location: page?pageID=" . $pageID);
    }
} else {
    $_SESSION['error'] = 'Invalid request.';
    header("Location: dashboard");
}
exit();
