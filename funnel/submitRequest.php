<?php
session_start();
include '../vendor/autoload.php';

if(isset($_POST['submitRequest'])){
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $hosting = htmlspecialchars($_POST['hosting'], ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
    $human = htmlspecialchars($_POST['human'], ENT_QUOTES, 'UTF-8');

    if($human === '12') {
        try {
            // Email content
            $body = "
                <h3>$name has sent you a message from your website.</h3>
                <p>This person is requesting that you contact them at your earliest convenience in regard to a capture page.</p>
                <p>Full Name: $name<br>Email Address: $email<br>Contact Number: $phone</p>
                <p>Hosting Needs: $hosting</p>
                <p>Message:<br>$message</p>
            ";

            // Initialize PHPMailer
            $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mailer->isSMTP();
            $mailer->setFrom("system@ezcapturepage.com", "EZ Capture Page");
            $mailer->CharSet = 'UTF-8';
            $mailer->Encoding = 'base64';

            // SMTP and DKIM configuration
            $mailer->DKIM_domain = 'ezcapturepage.com';
            $mailer->DKIM_private = dirname(__FILE__).'/key.private';
            $mailer->DKIM_selector = 'system';
            $mailer->DKIM_passphrase = '';
            $mailer->DKIM_identity = $mailer->From;
            $mailer->Host = 'ezcapturepage.com';
            $mailer->SMTPAuth = true;
            $mailer->Username = 'system@ezcapturepage.com';
            $mailer->Password = '1525WickedOne6924!';
            $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mailer->Port = 465;

            // Email settings
            $mailer->addAddress("support@ezcapturepage.com ", "EZ Capture Page");
            $mailer->isHTML(true);
            $mailer->Subject = "$name has sent you a Contact Request";
            $mailer->Body = $body;

            // Send the email
            $mailer->send();

            // Success message
            $_SESSION['success'] = 'Excellent! Your message was received. Someone from our office will get back with you shortly.';
        } catch (Exception $e) { // Catch PHPMailer exceptions
            $_SESSION['error'] = "Error sending message: " . $e->getMessage();
        }
    } else {
        // Human verification failed
        $_SESSION['error'] = "Oops! Human verification was not valid. Please try again.";
        header("Location: index.php");
        exit();
    }
} else {
    // POST request not sent properly
    $_SESSION['error'] = "Oops! Something went wrong. Please try again at a later time.";
    header("Location: index.php");
    exit();
}

// Redirect to the contact page
header("Location: index.php");
exit();
