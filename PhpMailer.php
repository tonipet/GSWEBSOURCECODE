
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendEmail($toEmail, $messageBody,$subject) {
    $mail = new PHPMailer(true);

    try {
        // Enable SMTP debugging for testing (0 = no debugging, 2 = maximum debugging)
        $mail->SMTPDebug = 0;

        // Set the mailer to use SMTP
        $mail->isSMTP();

        // SMTP server settings
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'GameSenseThesisMV@gmail.com';
        $mail->Password = 'mcgqmchbaqvqdfyc';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Sender and recipient
        $mail->setFrom('GameSenseThesisMV@gmail.com', 'GameSense Notification'); // Replace with your Gmail email and your name
        $mail->addAddress($toEmail); // Replace with the recipient's email

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $messageBody;

        // Send the email
        $mail->send();

        // Email sent successfully
        return true;
    } catch (Exception $e) {
        // Email sending failed
        return false;
    }
}


?>
