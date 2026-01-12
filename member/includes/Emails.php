<?php
namespace Infinite500;

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
        $mailer->setFrom("support@infinite500.com", "Infinite 500");
        $mailer->CharSet = 'UTF-8';
        $mailer->Encoding = 'base64';

        // Correct placement of SMTP and DKIM configuration
        $mailer->DKIM_domain = 'infinite500.com';
        $mailer->DKIM_private = dirname(__FILE__).'/key.private';
        $mailer->DKIM_selector = 'support';
        $mailer->DKIM_passphrase = '';
        $mailer->DKIM_identity = $mailer->From;
        $mailer->Host = 'infinite500.com';
        $mailer->SMTPAuth = true;
        $mailer->Username = 'support@infinite500.com';
        $mailer->Password = '1525WickedOne6924!';
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->Port = 465;
//        $mailer->SMTPDebug = 2;

        return $mailer;
    }
}
