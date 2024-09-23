<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<?php
try {
    // Database connection
    $userRole = $_SESSION['UserType']; // or however you store the user's role

    $db = getDB();

    // Get the UID from the URL
    $uid = isset($_GET['id']) ? $_GET['id'] : '';

    // Prepare and execute the query to fetch student profile data
    $sql = "SELECT B.FinalGrade, A.email, A.gender, A.noofhours, A.parentName, A.phone, A.sectionId, A.studentName 
            FROM tbl_androidstudentprofile A
            LEFT JOIN tbl_finalgrades B ON B.UID = A.UID 
            WHERE A.uid = :uid";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':uid', $uid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        // Extract values to variables
        $email = $row['email'] ?? '';
        $gender = $row['gender'] ?? '';
        $noofhours = $row['noofhours'] ?? '';
        $parentName = $row['parentName'] ?? '';
        $phone = $row['phone'] ?? '';
        $sectionId = $row['sectionId'] ?? '';
        $studentName = $row['studentName'] ?? '';
        $passFail = $row['FinalGrade'] ?? '';
    } else {
        throw new Exception("No profile found for UID: $uid");
    }

    // Fetch grades data
    $sql_grades = "SELECT subject, q1, q2, q3, q4, act1, act2, act3, final_grades 
                    FROM tbl_grades WHERE ProfileID = :uid";
    
    $stmt_grades = $db->prepare($sql_grades);
    $stmt_grades->bindParam(':uid', $uid);
    $stmt_grades->execute();
    $grades = $stmt_grades->fetchAll(PDO::FETCH_ASSOC);
    
    if ($grades === false) {
        throw new Exception("Error fetching grades data for UID: $uid");
    }

} catch (PDOException $e) {
    // Handle database connection or query errors
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    // Handle other errors
    echo "Error: " . $e->getMessage();
}
?>





<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-1"></i> Grades</span>
        <button type="button" class="btn btn-success" onclick="goBack()">Back to List</button>
    </div>
    <div class="card-body p-3">
        <!-- Personal Information Section -->
        <div class="row g-2">
            <div class="col-md-4 mb-2">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control form-control-sm" id="fullname" value="<?php echo htmlspecialchars($studentName); ?>" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label for="parentName" class="form-label">Parent Name</label>
                <input type="text" class="form-control form-control-sm" id="parentName" value="<?php echo htmlspecialchars($parentName); ?>" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label for="phone" class="form-label">Contact Number</label>
                <input type="text" class="form-control form-control-sm" id="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label for="Email" class="form-label">Email</label>
                <input type="text" class="form-control form-control-sm" id="Email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label for="passFail" class="form-label" required>Pass/Fail</label>
                <select class="form-control form-control-sm" id="passFail">
                    <option value="" <?php echo empty($passFail) ? 'selected' : ''; ?>>Select Grade</option>
                    <option value="Passed" <?php echo ($passFail == 'Passed') ? 'selected' : ''; ?>>Passed</option>
                    <option value="Failed" <?php echo ($passFail == 'Failed') ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="fileInput" class="form-label">Upload File</label>
                <input type="file" id="fileInput" name="file" class="form-control form-control-sm" accept=".xls, .xlsx">
            </div>
        </div>




        <?php if ($userRole != "admin"): ?>
        <!-- File Upload and Action Buttons -->
        <div class="row g-2">
            <div class="col-md-12 mb-2">
                <div class="d-flex justify-content-end gap-2">
                   <a href="templates/Template.xlsx" class="btn btn-primary btn-sm" download>Download Template</a>
                    <button type="button" class="btn btn-primary btn-sm" onclick="uploadFile()">View Excel File</button>
                    <button type="button" class="btn btn-success btn-sm ms-2" onclick="submitData()">Upload</button>
                    
                </div>
            </div>
        </div>

        <?php endif; ?>


        <!-- Result and Loading Message -->
        <div id="result" class="mt-3"></div>
        <div id="loading-message" class="alert alert-info" style="display: none;">
            <strong>Saving Please Wait</strong>
        </div>
        <div id="loading-messageError" class="alert alert-danger" style="display: none;">
            <strong></strong>
        </div>
    </div>
</div>
<?php if ($userRole != "admin"): ?>
<div class="card mb-4">
    <div class="card-body p-3">
        <!-- New fields for email subject and message body -->
        <form id="emailForm" method="POST">
            <div class="row g-2">
                <div class="col-md-12 mb-2">
                    <label for="email" class="form-label">To Email:</label>
                    <input type="email" id="email" name="email" class="form-control form-control-sm" value="<?php echo htmlspecialchars($email); ?>" readonly>
                </div>
                <div class="col-md-12 mb-2">
                    <label for="emailSubject" class="form-label">Subject:</label>
                    <input type="text" id="emailSubject" name="emailSubject" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-12 mb-2">
                    <label for="emailBody" class="form-label">Body:</label>
                    <textarea id="emailBody" name="emailBody" class="form-control form-control-sm" rows="4" required></textarea>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="d-flex justify-content-end">
                        <button type="button" id="submitBtn" class="btn btn-success btn-sm ms-2">Send Email</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Result and Loading Message -->
        <div id="Emailresult" class="mt-3"></div>
        <div id="Emailloading-message" class="alert alert-info" style="display: none;">
            <strong>Sending Please Wait</strong>
        </div>
        <div id="Emailloading-messageError" class="alert alert-danger" style="display: none;">
            <strong></strong>
        </div>
    </div>
</div>


<?php endif; ?>



<!-- Grades Table -->
<div class="mt-3">
    <h2>System Grades</h2>
    <table id="datatablesSimple" class="display table table-striped table-bordered">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Q1</th>
                <th>Q2</th>
                <th>Q3</th>
                <th>Q4</th>
                <th>Final Grades</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($grades): ?>
                <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                        <td><?php echo htmlspecialchars($grade['q1']); ?></td>
                        <td><?php echo htmlspecialchars($grade['q2']); ?></td>
                        <td><?php echo htmlspecialchars($grade['q3']); ?></td>
                        <td><?php echo htmlspecialchars($grade['q4']); ?></td>
                        <td><?php echo htmlspecialchars($grade['final_grades']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No grades found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script>





    let dataRows = [];

    function uploadFile() {
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        if (!file) {
         
            document.getElementById('loading-messageError').querySelector('strong').textContent = 'Please select a file.';
            document.getElementById('loading-messageError').style.display = 'block';
            return;
        }


        const id = getQueryParam('id');

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            let resultHtml = '<h2>Grades In Excel</h2>';
            resultHtml += '<table id="datatablesSimple" class="display table table-striped table-bordered">';
            resultHtml += '<thead><tr><th>Subject</th><th>Q1</th><th>Q2</th><th>Q3</th><th>Q4</th><th>Final Grades</th></tr></thead>';
            resultHtml += '<tbody>';
            
            // Skip the header row
            let isFirstRow = true;
            dataRows = [];
            rows.forEach(row => {
    if (isFirstRow) {
        isFirstRow = false;
        return;
    }

    const [subject, q1, q2, q3, q4, final_grades] = row;


   

    // Check if subject is not empty before processing the row
    if (subject) {
        dataRows.push({ 
            subject: subject,
            q1: q1 || 0,
            q2: q2 || 0,
            q3: q3 || 0,
            q4: q4 || 0,
            final_grades: final_grades || 0,
            id: id
         
        });

       
        resultHtml += `
            <tr>
                <td>${subject || ''}</td>
                <td>${q1 || 0}</td>
                <td>${q2 || 0}</td>
                <td>${q3 || 0}</td>
                <td>${q4 || 0}</td>
                <td>${final_grades || 0}</td>
            </tr>
        `;
    }
});

            resultHtml += '</tbody></table>';
            document.getElementById('result').innerHTML = resultHtml;

           
           
         
        };
        reader.readAsArrayBuffer(file);
    }


    function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

    function submitData() {
        if (dataRows.length === 0) {

        document.getElementById('loading-messageError').querySelector('strong').textContent = 'No data to submit. Please upload a file first.';
        document.getElementById('loading-messageError').style.display = 'block';
          
            return;
        }

        if ( document.getElementById('passFail').value === ""){
            document.getElementById('loading-messageError').querySelector('strong').textContent = 'Please input Final Grade';
              document.getElementById('loading-messageError').style.display = 'block';
            return;
        }
        const Profileid = getQueryParam('id');
        passFail = document.getElementById('passFail').value;
        dataRows.push({ passFail: passFail });
        dataRows.push({ Profileid: Profileid });
         console.log(dataRows);
       
        document.getElementById('loading-message').style.display = 'block';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'FuncUploadGrades.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                //console.log(xhr.responseText);
                document.getElementById('loading-message').style.display = 'none';
                 window.location.reload();
            } else {
                alert('An error occurred while submitting data.');
            }
        };
        xhr.send(JSON.stringify(dataRows));
    }

    function goBack() {
        window.history.back();
    }




 document.addEventListener('DOMContentLoaded', function() {
        let submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                var form = document.getElementById('emailForm');
                var formData = new FormData(form);
                var tableHTML = document.querySelector('#datatablesSimple').outerHTML;
                formData.append('gradesTable', tableHTML);

                document.getElementById('Emailloading-message').style.display = 'block';

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'FuncSendemailGrades.php', true);

                xhr.onload = function () {
                    document.getElementById('Emailloading-message').style.display = 'none';

                    if (xhr.status >= 200 && xhr.status < 300) {
                        document.getElementById('Emailresult').innerHTML = '<div class="alert alert-success">Email sent successfully!</div>';
                    } else {
                        document.getElementById('Emailloading-messageError').style.display = 'block';
                        document.getElementById('Emailloading-messageError').innerHTML = '<strong>Failed to send email.</strong>';
                    }
                };

                xhr.send(formData);
            });
        } else {
            console.error('submitBtn element not found');
        }
    });



</script>

<?php include 'layout/footer.php'; ?>
