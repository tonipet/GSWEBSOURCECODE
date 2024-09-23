<?php
include 'layout/header.php';
include 'layout/sidebar.php';

$db = getDB();

// Get JSON data from the request
$data = file_get_contents('php://input');
$rows = json_decode($data, true);

if (json_last_error() === JSON_ERROR_NONE) {
    $ProfileID = "";
    $passFail = "";

    try {
        foreach ($rows as $row) {
           
            if (isset($row['Profileid'])) {
                $ProfileID = $row['Profileid'];
            }
            
         if (isset($row['passFail'])) {
                $passFail = $row['passFail'];
            }
        }

        // Check if record exists
        $sql = "SELECT COUNT(*) FROM tbl_finalgrades WHERE UID = :uid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':uid', $ProfileID);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            // Update existing record
            $sql = "UPDATE tbl_finalgrades SET 
                    FinalGrade = :FinalGrade
                    WHERE UID = :uid";
        } else {
            // Insert new record
            $sql = "INSERT INTO tbl_finalgrades (UID, FinalGrade) 
                    VALUES (:uid, :FinalGrade)";
        }

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':FinalGrade', $passFail);
        $stmt->bindParam(':uid', $ProfileID);
        $stmt->execute();

        // Delete old grades
        $sql = "DELETE FROM tbl_grades WHERE ProfileID = :ProfileID";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ProfileID', $ProfileID);
        $stmt->execute();

        foreach ($rows as $row) {
            $subject = $row['subject'];
            $q1 = $row['q1'];
            $q2 = $row['q2'];
            $q3 = $row['q3'];
            $q4 = $row['q4'];
            $final_grades = $row['final_grades'];
            $ProfileID = $row['id'];

            $sql = "INSERT INTO tbl_grades (subject, ProfileID, q1, q2, q3, q4, final_grades)
                    VALUES (:subject, :ProfileID, :q1, :q2, :q3, :q4, :final_grades)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':q1', $q1);
            $stmt->bindParam(':q2', $q2);
            $stmt->bindParam(':q3', $q3);
            $stmt->bindParam(':q4', $q4);
            $stmt->bindParam(':final_grades', $final_grades);
            $stmt->bindParam(':ProfileID', $ProfileID);
            $stmt->execute();
        }

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'General error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
}

include 'layout/footer.php';
?>
