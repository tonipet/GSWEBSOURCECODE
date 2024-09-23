<?php
include 'phpmailer.php'; // Include PHPMailer functions

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $toEmail = $_POST['email']; // Match with the form field's name
    $subject = $_POST['emailSubject']; // Match with the form field's name
    $messageBody = $_POST['emailBody']; // Match with the form field's name
    $gradesTable = $_POST['gradesTable']; // Get the table HTML

    // Load the HTML template
    $templateFile = 'email_template.html';
    $templateContent = file_get_contents($templateFile);

    // Replace placeholders in the template with actual data
    $htmlMessage = str_replace(['<?php echo $messageBody; ?>', '<?php echo $gradesTable; ?>'], [$messageBody, $gradesTable], $templateContent);

    // Call the sendEmail function
    $result = sendEmail($toEmail, $htmlMessage, $subject);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Email sent successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
    }
}
?>
