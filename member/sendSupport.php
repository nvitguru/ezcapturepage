<?php
session_start();
require_once '../includes/session.php';
require_once '../vendor/autoload.php';
require_once '../includes/settings.php';

// Instantiate database connection
$conn = $pdo->open();

function createSupportMessage($fname, $lname, $email, $question, $date) {
    $systemName = htmlspecialchars(SYSTEM_NAME);
    $fname = htmlspecialchars($fname);
    $lname = htmlspecialchars($lname);
    $email = htmlspecialchars($email);
    $question = htmlspecialchars($question);

    return "
        <h4>A member has sent a support ticket on {$date}</h4>
        <p>Name: {$fname} {$lname}<br>Email: {$email}</p>
        <p>Support Question:<br>{$question}</p>
        <p>Please make sure to answer this email in a timely manner.</p>
        <br>
        <p><strong>{$systemName} System</strong></p>
    ";
}

if (isset($_POST['sendSupport'])) {
    try {
        $now = date('F d, Y');
        $email = $_POST['email'] ?? '';
        $fname = $_POST['fname'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $question = $_POST['question'] ?? '';

        // Create the support message HTML
        $supportMessage = createSupportMessage($fname, $lname, $email, $question, $now);

        // Set up mailer
        $supportMailer = \EZCapture\Support::getMailer();
        $supportMailer->addAddress('support@ezcapturepage.com', 'EZ Capture Page Support Team');
        $supportMailer->isHTML(true);
        $supportMailer->Subject = $fname ." ". $lname ." ". 'Has Sent A Support Question';
        $supportMailer->Body = $supportMessage;

        // Send email and handle response
        if ($supportMailer->send()) {
            $_SESSION['success'] = '<strong>Support Message Sent Successfully!</strong> One of our support members will be in touch with you shortly.';
        } else {
            throw new Exception('Failed to send support message.');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = '<strong>Oops! Something went wrong.</strong> ' . $e->getMessage();
    }
    header('Location: support.php');
    exit;
}

// Close the database connection
$pdo->close();
