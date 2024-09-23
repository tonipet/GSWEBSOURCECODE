<?php
include 'layout/header.php';
include 'layout/sidebar.php';

// Include the database connection function and get the connection
$db = getDB();

// Initialize variables
$id = $fullname = $idnumber = $password = $usertype = $active = $SectionUID = $Section = $EmailAddress= "";
$action = "Add"; // Default action

// Check if ID is set for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = "Edit";

    // Fetch the record from the database
    if ($db) {
        $query = "SELECT FullName,EmailAddress, IDNumber, Password, Usertype, Active, SectionUID, Section FROM tblUsers WHERE UserID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Populate the form with existing data
        if ($record) {
            $fullname = htmlspecialchars($record['FullName']);
            $EmailAddress = htmlspecialchars($record['EmailAddress']);
            $idnumber = htmlspecialchars($record['IDNumber']);
            $password = htmlspecialchars($record['Password']);
            $usertype = htmlspecialchars($record['Usertype']);
            $active = htmlspecialchars($record['Active']);
            $SectionUID = htmlspecialchars($record['SectionUID']);
            $Section = htmlspecialchars($record['Section']);
        }
    }
}
?>

<div class="container mt-4">
    <h2><?php echo $action; ?> User</h2>
    <form action="AdminSaveuser.php" method="POST">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <div class="mb-3">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $fullname; ?>" required>
        </div>
        <div class="mb-3">
            <label for="fullname" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="EmailAddress" name="EmailAddress" value="<?php echo $EmailAddress; ?>" required>
        </div>

        <div class="mb-3">
            <label for="idnumber" class="form-label">ID Number</label>
            <input type="text" class="form-control" id="idnumber" name="idnumber" value="<?php echo $idnumber; ?>" required>
        </div>

        <!-- <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
        </div> -->

        <div class="mb-3">
            <label for="usertype" class="form-label">User Type</label>
            <select class="form-select" id="usertype" name="usertype" required>
                <option value="" disabled>Select User Type</option>
                <option value="admin" <?php if ($usertype == "admin") echo "selected"; ?>>Admin</option>
                <option value="user" <?php if ($usertype == "user") echo "selected"; ?>>User</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="active" class="form-label">Active</label>
            <select class="form-select" id="active" name="active" required>
                <option value="1" <?php if ($active == "1") echo "selected"; ?>>Yes</option>
                <option value="0" <?php if ($active == "0") echo "selected"; ?>>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="section" class="form-label">Section</label>
            <select class="form-select" id="section" name="SectionUID" required>
                <!-- Options will be populated by JavaScript -->
            </select>
        </div>

        <input type="text" class="form-control" id="Section" name="Section" value="<?php echo $Section;?>" hidden>

        <button type="submit" class="btn btn-primary">Save</button>
     
        <a href="AdminAddUser.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php
include 'layout/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sectionSelect = document.getElementById('section');
    const sectionValueInput = document.getElementById('Section');

    // Fetch sections from Firebase and populate the dropdown
    firebase.database().ref('user_section/').on('value', function(snapshot) {
        const data = snapshot.val();
        sectionSelect.innerHTML = '<option value="">Select Section</option>'; // Clear existing options

        if (data) {
            Object.keys(data).forEach(key => {
                const section = data[key];
                const option = document.createElement('option');
                option.value = section.UID || 'N/A';
                option.textContent = section.sectionName || 'N/A';
                sectionSelect.appendChild(option);
            });

            // Set the selected option if applicable
            const sectionUID = <?php echo json_encode($SectionUID); ?>;
            if (sectionUID) {
                const options = sectionSelect.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === sectionUID) {
                        sectionSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }
    });

    // Update Section input when section dropdown changes
    sectionSelect.addEventListener('change', function() {
        const selectedOption = sectionSelect.options[sectionSelect.selectedIndex];
        sectionValueInput.value = selectedOption.textContent;
    });
});

</script>