<?php
namespace EZCapture;

use PHPMailer\PHPMailer\PHPMailer;

class Emails
{
    /**
     * @return PHPMailer
     */
    public static function getMailer()
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->setFrom("support@ezcapturepage.com", "EZ Capture Page");
        $mailer->CharSet = 'UTF-8';
        $mailer->Encoding = 'base64';

        // Correct placement of SMTP and DKIM configuration
        $mailer->DKIM_domain = 'ezcapturepage.com';
        $mailer->DKIM_private = dirname(__FILE__).'/key.private';
        $mailer->DKIM_selector = 'support';
        $mailer->DKIM_passphrase = '';
        $mailer->DKIM_identity = $mailer->From;
        $mailer->Host = 'ezcapturepage.com';
        $mailer->SMTPAuth = true;
        $mailer->Username = 'support@ezcapturepage.com';
        $mailer->Password = '1525WickedOne6924!';
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->Port = 465;
//        $mailer->SMTPDebug = 2;

        return $mailer;
    }
}
