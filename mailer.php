<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendMail($to, $subject, $messageBody) {
    $mail = new PHPMailer(true);

    try {
        //  Gmail SMTP Settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rkarthik0806@gmail.com'; //  Gmail ID
        $mail->Password = 'eigt uhas zvxs daml';    //  Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //  Extra Settings (Fix repeated send issue)
        $mail->SMTPKeepAlive = false; // Important: avoid reuse issues
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Timeout = 30; // 30 seconds timeout

        //  From & To
        $mail->setFrom('rkarthik0806@gmail.com', 'Contact Form');
        $mail->addAddress($to);

        //  Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        //  Send Mail
        if ($mail->send()) {
            return true;
        } else {
            error_log("Mail not sent: " . $mail->ErrorInfo);
            return false;
        }
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    } finally {
        //  Clean up after each send
        $mail->clearAddresses();
        $mail->clearAttachments();
    }
}
?>
