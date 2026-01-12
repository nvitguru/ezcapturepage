<?php
/** @var Database $pdo */

require_once '../vendor/autoload.php';
session_start();
include '../includes/settings.php';
include '../includes/conn.php';

$url = parse_url($_SERVER['REQUEST_URI']);
parse_str($url['query'], $searchParams);
$customerId = (string) $searchParams['customerId'];
if (!$customerId) {
    header(sprintf('Location: https://%s/registration/', SYSTEM_URL), true, 404);
    exit;
}
$stripe = new EZCapture\StripeSubscriptions();
$customer = $stripe->retrieveCustomer($customerId);
[$fname, $lname] = explode(' ', $customer->name, 2);
$email = $customer->email;
['referral_code' => $referralCode, 'membership' => $membership] = $customer->metadata;

try {
    $conn = $pdo->open();
    $now = date('Y-m-d');

    do {
        // Generate a unique member ID
        $userID = mt_rand(1000, 99999999);
        $result = $conn->query(sprintf('SELECT COUNT(id) AS exist FROM users WHERE userID = %d;', $userID));
    } while ($result->fetch()['exist'] > 0);

    // Encrypt the password
    $tempPass = "Capture" . $userID;
    $encodePass = password_hash($tempPass, PASSWORD_DEFAULT);

    $userStmt = $conn->prepare("INSERT INTO users (userID, fname, lname, email, password, level, affiliate, created) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $userStmt->execute([$userID, $fname, $lname, $email, $encodePass, $membership, $referralCode, $now]);

    $memberMessage = "
            <p>Hello ". $fname .",</p>
            <p>Welcome to the EZCapturePage.com family! We are excited to have you on board and are committed to helping you succeed with your online marketing efforts.</p>
            <p>Below are your login details to get started:</p>
            <p>Login Email: ". $email ."<br>Temporary Password: ". $tempPass ."</p>
            <p>For security reasons, we recommend you change your password immediately after your first login. Follow these simple steps:</p>
            <ol>
                <li>Go to https://".SYSTEM_URL."/member</li>
                <li>Enter your login email and temporary password.</li>
                <li>Once logged in, navigate to the “Settings > Password” section.</li>
                <li>Put your temporary password in the \"Current Password\" field and your in the \"New Password\" and \"Confirm Password\" fields.</li>
            </ol>
            <p>If you need any assistance or have questions, don’t hesitate to reach out to our support team at ".SYSTEM_SUPPORT_EMAIL.". We're here to help you every step of the way.</p>
            <p><strong>What’s next?</strong><br>We encourage you to explore our platform and start building your custom capture pages. Whether you are just getting started or are looking to take your online marketing to the next level, EZCapturePage.com offers powerful tools to support your journey.</p>
            <p><strong>Thank you for choosing us, and we look forward to seeing what you create!</strong></p>
            <h4>".SYSTEM_NAME." Support Team</h4>
            <p>".SYSTEM_SUPPORT_EMAIL."<br>".SYSTEM_URL."</p>
            <p><img src='https://".SYSTEM_URL."/images/ez-capture-page-logo.png' style='max-width: 200px;'></p>
            ";

    $memberMailer = \EZCapture\Emails::getMailer();
    $memberMailer->addAddress($email, sprintf('%s %s', $fname, $lname));

    $memberMailer->isHTML(true);
    $memberMailer->Subject = 'Welcome to EZCapturePage.com – Your Account is Ready!';
    $memberMailer->Body = $memberMessage;
    $memberMailer->send();
    $_SESSION['success'] = 'Registration Successful! A welcome email has been sent out to you with your login credentials. Please make sure to check both the inbox and spam folder for this email.';
    header("location: ../member");

} catch (PDOException $e) {
    error_log("PDO Exception: " . $e->getMessage(), 0);
    $_SESSION['error'] = 'An error occurred during registration: ' . $e->getMessage();
    $pdo->close();
    header("location: ../registration");
}