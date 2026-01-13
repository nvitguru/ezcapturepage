<?php
/**
 * sendSupport.php
 *
 * Responsibilities:
 * - Handle support form submissions from authenticated members
 * - Send a support email to the support inbox
 * - Set a session flash message indicating success/failure
 */

session_start();

require_once '../includes/session.php';   // Auth/session bootstrap (assumed to provide $user and/or guards)
require_once '../vendor/autoload.php';
require_once '../includes/settings.php';

/**
 * Build the support email HTML body.
 * Escapes all user-provided values to prevent HTML injection.
 */
function createSupportMessage(string $fname, string $lname, string $email, string $question, string $date): string
{
    $systemName = htmlspecialchars((string)SYSTEM_NAME, ENT_QUOTES, 'UTF-8');

    $fname    = htmlspecialchars($fname, ENT_QUOTES, 'UTF-8');
    $lname    = htmlspecialchars($lname, ENT_QUOTES, 'UTF-8');
    $email    = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $question = htmlspecialchars($question, ENT_QUOTES, 'UTF-8');

    return "
        <h4>A member has sent a support ticket on {$date}</h4>
        <p>Name: {$fname} {$lname}<br>Email: {$email}</p>
        <p>Support Question:<br>{$question}</p>
        <p>Please make sure to answer this email in a timely manner.</p>
        <br>
        <p><strong>{$systemName} System</strong></p>
    ";
}

/**
 * Only accept valid POST submissions.
 */
if (!isset($_POST['sendSupport'])) {
    header('Location: support.php');
    exit;
}

try {
    /**
     * Collect and normalize inputs.
     */
    $now = date('F d, Y');

    $email    = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
    $fname    = isset($_POST['fname']) ? trim((string)$_POST['fname']) : '';
    $lname    = isset($_POST['lname']) ? trim((string)$_POST['lname']) : '';
    $question = isset($_POST['question']) ? trim((string)$_POST['question']) : '';

    /**
     * Build email body.
     */
    $supportMessage = createSupportMessage($fname, $lname, $email, $question, $now);

    /**
     * Configure mailer.
     */
    $supportMailer = \EZCapture\Support::getMailer();
    $supportMailer->addAddress('support@ezcapturepage.com', 'EZ Capture Page Support Team');
    $supportMailer->isHTML(true);
    $supportMailer->Subject = $fname . " " . $lname . " " . 'Has Sent A Support Question';
    $supportMailer->Body = $supportMessage;

    /**
     * Send and set flash message.
     */
    if ($supportMailer->send()) {
        $_SESSION['success'] = '<strong>Support Message Sent Successfully!</strong> One of our support members will be in touch with you shortly.';
    } else {
        throw new Exception('Failed to send support message.');
    }

} catch (Exception $e) {
    /**
     * Preserve existing behavior: include exception message in user error output.
     * (If you want to stop exposing internal error details, tell me and I’ll adjust.)
     */
    $_SESSION['error'] = '<strong>Oops! Something went wrong.</strong> ' . $e->getMessage();
}

header('Location: support.php');
exit;
