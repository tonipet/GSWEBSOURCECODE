<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>
<?php
//Include the database connection function and get the connection
$db = getDB();

// Fetch grades data for all students
$sql = "SELECT UID, FinalGrade FROM tbl_finalgrades";
$stmt = $db->prepare($sql);
$stmt->execute();
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert grades to JSON for use in JavaScript
echo '<script>';
echo 'const gradesData = ' . json_encode($grades) . ';';
echo '</script>';
?>
<style>
    .highlight-red {
        background-color: #ffcccc; /* Light red background */
    }
</style>
<h1 class="mt-4" id="HeaderTitle" >Student List</h1>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-1" ></i></span>
        <a href="AdminSectionList.php" class="btn btn-success">Back to List</a>
    </div>
    <div class="card-body">
        <table id="datatablesSectionList" style="display: none;">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Gender</th>
                    <th>Email Address</th>
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Student Name</th>
                    <th>Gender</th>
                    <th>Email Address</th>
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
            </tfoot>
            <tbody></tbody>
        </table>
    </div>
</div>
<?php include 'layout/footer.php'; ?>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch and display the section list when the page loads
        GetSectionList();

        // Event delegation for dynamically created buttons (if needed)
        const datatablesSectionList = document.getElementById('datatablesSectionList');
        if (datatablesSectionList) {
            datatablesSectionList.addEventListener('click', function (e) {
                if (e.target.classList.contains('edit-btn')) {
                    const id = e.target.getAttribute('data-id');
                    window.location.href = `SectionAddEdit.php?id=${id}`;
                }

                if (e.target.classList.contains('delete-btn')) {
                    const id = e.target.getAttribute('data-id');
                    console.log('Delete button clicked for ID:', id);
                }
            });
        }
    });

    function GetSectionList() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const sectionName = urlParams.get('sectionName');
    document.getElementById('HeaderTitle').innerText = sectionName;

    const tableBody = document.querySelector('#datatablesSectionList tbody');
    const datatablesSectionList = document.getElementById('datatablesSectionList');

    if (!tableBody || !datatablesSectionList) {
        console.error('Table body or table element not found');
        return;
    }

    firebase
        .database()
        .ref("user_profile/")
        .orderByChild("sectionId")
        .equalTo(id)
        .on('value', function (snap) {
            const data = snap.val();
            tableBody.innerHTML = ''; // Clear existing rows

            if (data) {
                Object.keys(data).forEach(key => {
                    const childData = data[key];
                    const row = document.createElement('tr');

                    // Find the corresponding grade for the current student
                    const gradeEntry = gradesData.find(grade => grade.UID === childData.uid);
                    const finalGrade = gradeEntry ? gradeEntry.FinalGrade.trim() : '';

                    // Apply the red highlight if the grade is "Failed"
                    if (finalGrade === 'Failed') {
                        row.classList.add('highlight-red');
                    }

                    const viewButton = `
                        <button class="btn btn-primary btn-sm" onclick="window.location.href='StudenGameStats.php?id=${childData.uid}'">
                           <i class="fas fa-gamepad"></i>
                        </button>`;
                    const gradeButton = `
                        <button class="btn btn-primary btn-sm" onclick="window.location.href='StudentGrades.php?id=${childData.uid}'">
                           <i class="fas fa-graduation-cap"></i>
                        </button>`;

                    row.innerHTML = `
                        <td>${childData.studentName || 'N/A'}</td>
                        <td>${childData.gender || 'N/A'}</td>
                        <td>${childData.email || 'N/A'}</td>
                        <td>${finalGrade}</td> <!-- Add the grade here -->
                        <td>${viewButton}${gradeButton}</td>`;

                    tableBody.appendChild(row);
                });

                // Initialize or reinitialize the DataTable after adding rows
                new simpleDatatables.DataTable(datatablesSectionList);

                // Reapply custom styles after DataTable initialization
                setTimeout(() => {
                    document.querySelectorAll('#datatablesSectionList tbody tr').forEach(row => {
                        const gradeCell = row.querySelector('td:nth-child(4)');
                        if (gradeCell && gradeCell.innerText.trim().toLowerCase() === 'failed') {
                            row.classList.add('highlight-red');
                        }
                    });
                }, 100); // Adjust the delay as needed

                datatablesSectionList.style.display = 'table';
            }
        });
}
</script>
