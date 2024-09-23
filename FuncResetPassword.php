<?php
session_start(); // Start the session

include './phpConfig/config.php'; // Include the database connection

// Retrieve form data
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id) {
    try {
        $db = getDB(); // Get the database connection

        // Prepare the SQL statement to fetch the user details
        $query = "SELECT UserID, IDNumber FROM tblUsers WHERE UserID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Use the IDNumber as the new password
            $newPassword = $user['IDNumber'];
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT); // Hash the new password

            // Prepare the SQL statement to update the password
            $query = "UPDATE tblUsers SET Password = :password WHERE UserID = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $_SESSION['reset_message'] = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                                    Password has been reset successfully. The new password is: <strong>$newPassword</strong>.
                                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                                </div>";
            } else {
                $_SESSION['reset_message'] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                                    Failed to reset the password. Please try again.
                                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                                </div>";
            }
        } else {
            $_SESSION['reset_message'] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                                No user found with that ID Number.
                                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                            </div>";
        }
    } catch (PDOException $e) {
        $_SESSION['reset_message'] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                            Database error: " . htmlspecialchars($e->getMessage()) . "
                                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                        </div>";
    }
} else {
    $_SESSION['reset_message'] = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                        Please enter an ID Number.
                                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                    </div>";
}

// Redirect back to the password reset form
header("Location: AdminAddUser.php");
exit();
?>
