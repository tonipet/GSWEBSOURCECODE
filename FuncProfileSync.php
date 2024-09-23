
<?php 
session_start(); // Start the session

include './phpConfig/config.php'; // Include the database connection

$db = getDB();

if ($db) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        foreach ($data as $key => $user) {
            // Retrieve user data
            $email = isset($user['email']) ? $user['email'] : '';
            $gender = isset($user['gender']) ? $user['gender'] : '';
            $noofhours = isset($user['noofhours']) ? $user['noofhours'] : '';
            $parentName = isset($user['parentName']) ? $user['parentName'] : '';
            $phone = isset($user['phone']) ? $user['phone'] : '';
            $sectionId = isset($user['sectionId']) ? $user['sectionId'] : '';
            $studentName = isset($user['studentName']) ? $user['studentName'] : '';
            $uid = isset($user['uid']) ? $user['uid'] : '';

            // Check if record exists
            $sql = "SELECT COUNT(*) FROM tbl_androidstudentprofile WHERE uid = :uid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                // Update existing record
                $sql = "UPDATE tbl_androidstudentprofile SET 
                        email = :email,
                        gender = :gender,
                        noofhours = :noofhours,
                        parentName = :parentName,
                        phone = :phone,
                        sectionId = :sectionId,
                        studentName = :studentName
                        WHERE uid = :uid";
            } else {
                // Insert new record
                $sql = "INSERT INTO tbl_androidstudentprofile (email, gender, noofhours, parentName, phone, sectionId, studentName, uid) 
                        VALUES (:email, :gender, :noofhours, :parentName, :phone, :sectionId, :studentName, :uid)";
            }

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':noofhours', $noofhours);
            $stmt->bindParam(':parentName', $parentName);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':sectionId', $sectionId);
            $stmt->bindParam(':studentName', $studentName);
            $stmt->bindParam(':uid', $uid);

            if ($stmt->execute()) {
                echo "Record updated/inserted successfully for UID: $uid<br>";
            } else {
                echo "Error: " . implode(", ", $stmt->errorInfo()) . "<br>";
            }
        }

        $db = null; // Close connection
    }
}
?>
