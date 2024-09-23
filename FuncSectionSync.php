
<?php 
session_start(); // Start the session

include './phpConfig/config.php'; // Include the database connection

$db = getDB();


	 	
		
	


if ($db) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        foreach ($data as $key => $user) {
            // Retrieve user data
            $sectionDescription = isset($user['sectionDescription']) ? $user['sectionDescription'] : '';
            $sectionName = isset($user['sectionName']) ? $user['sectionName'] : '';
            $UID = isset($user['UID']) ? $user['UID'] : '';

            // Check if record exists
            $sql = "SELECT COUNT(*) FROM tbl_androidstudentsection WHERE uid = :uid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                // Update existing record
                $sql = "UPDATE tbl_androidstudentsection SET 
                        sectionDescription = :sectionDescription,
                        sectionName = :sectionName
                        WHERE UID = :UID";
            } else {
                // Insert new record
                $sql = "INSERT INTO tbl_androidstudentsection (sectionDescription, sectionName, UID) 
                        VALUES (:sectionDescription, :sectionName,:UID)";
            }

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':sectionDescription', $sectionDescription);
            $stmt->bindParam(':sectionName', $sectionName);
            $stmt->bindParam(':UID', $UID);

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
