
<?php
include './phpConfig/config.php';
$db = getDB();

$id = isset($_POST['id']) ? $_POST['id'] : null;
$fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
$idnumber = isset($_POST['idnumber']) ? $_POST['idnumber'] : '';
// $password = isset($_POST['password']) ? $_POST['password'] : '';
$usertype = isset($_POST['usertype']) ? $_POST['usertype'] : '';
$active = isset($_POST['active']) ? $_POST['active'] : '';
$EmailAddress = isset($_POST['EmailAddress']) ? $_POST['EmailAddress'] : '';
$SectionUID = isset($_POST['SectionUID']) ? $_POST['SectionUID'] : '';
$Section = isset($_POST['Section']) ? $_POST['Section'] : '';


if ($id) {
        // $hashedPassword = password_hash($idnumber, PASSWORD_BCRYPT);
        $query = "UPDATE tblusers SET fullname = :fullname, idnumber = :idnumber, usertype = :usertype, active = :active, SectionUID = :SectionUID, Section = :Section, EmailAddress = :EmailAddress WHERE UserID = :id";
        $stmt = $db->prepare($query);
   
         $stmt->bindParam(':id', $id);
} else {
    // Insert new record
    $hashedPassword = password_hash($idnumber, PASSWORD_BCRYPT);
    $query = "INSERT INTO tblusers (fullname, idnumber, password, usertype, active, SectionUID, Section, EmailAddress) VALUES (:fullname, :idnumber, :password, :usertype, :active, :SectionUID, :Section, :EmailAddress)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $hashedPassword);
}

$stmt->bindParam(':fullname', $fullname);
$stmt->bindParam(':idnumber', $idnumber);
$stmt->bindParam(':usertype', $usertype);
$stmt->bindParam(':active', $active);
$stmt->bindParam(':SectionUID', $SectionUID);
$stmt->bindParam(':EmailAddress', $EmailAddress);
$stmt->bindParam(':Section', $Section);

try {
    if ($stmt->execute()) {
            header("Location: AdminAddUser.php");
    } else {
        echo "Error saving data.";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
