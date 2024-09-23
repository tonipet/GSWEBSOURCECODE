<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
function sendEmail($toEmail, $messageBody, $subject) {
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'GameSenseThesisMV@gmail.com';
        $mail->Password = 'mcgqmchbaqvqdfyc';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('GameSenseThesisMV@gmail.com', 'Game Sense');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $messageBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$response = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toEmail = $_POST['email'];
    $messageBody = $_POST['message'];
    $subject = $_POST['subject'];

    if (sendEmail($toEmail, $messageBody, $subject)) {
        $response = 'Email sent successfully!';
    } else {
        $response = 'Failed to send email.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email</title>
</head>
<body>
    <h1>Send an Email</h1>
    <form action="test.php" method="post">
        <label for="email">Recipient Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="5" cols="40" required></textarea><br><br>

        <button type="submit">Send Email</button>
    </form>

    <?php if ($response): ?>
        <p><?php echo htmlspecialchars($response); ?></p>
    <?php endif; ?>
</body>
</html>
