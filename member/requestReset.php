<?php
include '../vendor/autoload.php';
include '../includes/settings.php';
include '../includes/conn.php';
 session_start();

if(isset($_POST['requestReset'])){
    $email = $_POST['forgotEmail'];

    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    if($row){
        $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = substr(str_shuffle($set), 0, 15);

        try {
            $stmt = $conn->prepare("UPDATE users SET resetCode = :code WHERE userID = :userID");
            $stmt->execute(['code' => $code, 'userID' => $row['userID']]);

            $message = "
					<h2>Password Reset</h2>
					<p>A password reset was just submitted for your ". SYSTEM_NAME ." account.</p>
					<p><strong>Your Account:</strong><br>Email: ".$email."</p>
					<p>Please click the link below to reset your password.</p>
					<p><a href='https://".SYSTEM_URL."/member/reset?code=".$code."&user=".$row['userID']."'>https://".SYSTEM_URL."/reset?code=".$code."&user=".$row['userID']."</a></p>
					<p>If you did not request a password reset, please disregard this email.</p>
					<h4>".SYSTEM_NAME." Support Team</h4>
                    <p>".SYSTEM_SUPPORT_EMAIL."<br>".SYSTEM_URL."</p>
                    <p><img src='https://".SYSTEM_URL."/images/ez-capture-page-logo.png' style='max-width: 200px;'></p>
				";

            $resetMailer = \EZCapture\Emails::getMailer();
            $resetMailer->addAddress($email);
            $resetMailer->isHTML(true);
            $resetMailer->Subject = SYSTEM_NAME . ' Password Reset Request';
            $resetMailer->Body = $message;

            $resetMailer->send();
            $_SESSION['success'] = 'Password reset link sent! Check your inbox or spam folder for reset email.';
        }
        catch (Exception $e) {
            $_SESSION['error'] = 'Message could not be sent. Mailer Error: ' . $e->getMessage();
        }
    }
    else{
        $_SESSION['error'] = 'Oops! The email was not found in our system.';
    }

    $pdo->close();
    header('Location: forgot-password');
    exit;
}
else{
    $_SESSION['error'] = 'Input email associated with account';
    header('Location: forgot-password');
    exit;
}