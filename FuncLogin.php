<?php
session_start(); // Start the session

include './phpConfig/config.php'; // Include the database connection

// Retrieve form data
$idnumber = isset($_POST['idnumber']) ? $_POST['idnumber'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($idnumber && $password) {
    try {
        $db = getDB(); // Get the database connection

        // Prepare the SQL statement to avoid SQL injection
        $query = "SELECT UserID, Section,Password, FullName,UserType, Active FROM tblusers WHERE IDNumber = :idnumber";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':idnumber', $idnumber);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['Password'])) {
                // Check if the user is active
                if ($user['Active'] == '1') {
                    // Set session variables
                    $_SESSION['UserID'] = $user['UserID'];
                    $_SESSION['UserType'] = $user['UserType'];
                    $_SESSION['Section']= $user['Section'];
                    $_SESSION['FullName']= $user['FullName'];
                    // Redirect to the dashboard or main page
                    header("Location: AdminSectionList.php");
                    exit();
                } else {
                    $_SESSION['login_message'] = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                                        Your account is not active.
                                                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                                    </div>";
                }
            } else {
                $_SESSION['login_message'] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                                    Invalid password.
                                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                                </div>";
            }
        } else {
            $_SESSION['login_message'] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                                No user found with that ID Number.
                                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                            </div>";
        }
    } catch (PDOException $e) {
        $_SESSION['login_message'] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                            Database error: " . htmlspecialchars($e->getMessage()) . "
                                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                        </div>";
    }
} else {
    $_SESSION['login_message'] = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                        Please enter both ID Number and Password.
                                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                    </div>";
}

// Redirect back to the login page
header("Location: index.php");
exit();
?>
