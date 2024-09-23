<?php 
include 'layout/header.php'; 
include 'layout/sidebar.php'; 

// Start the session if not already started


// Ensure $_SESSION['Section'] is set
$UserType = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : '';
$sectionFilter = isset($_SESSION['Section']) ? $_SESSION['Section'] : '';
?>

<h1 class="mt-4"></h1>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-1"></i> Section List</span>
    </div>
    <div class="card-body">
        <table id="datatablesSectionList" style="display: none;">
            <thead>
                <tr>
                    <th>Section Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Section Name</th>
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
        // Pass the PHP session value to JavaScript

        
        const UserType = "<?php echo $UserType; ?>";
        const sectionFilter = "<?php echo $sectionFilter; ?>";
     
        if(UserType === "admin"){
            GetSectionList();
        }else{
            GetSectionList(sectionFilter);
        }
     
        
        const datatablesSectionList = document.getElementById("datatablesSectionList");
        if (datatablesSectionList) {
            document.getElementById('datatablesSectionList').addEventListener('click', function (e) {
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

        sync();
        syncSection();
    });

    function GetSectionList(sectionFilter) {


        const tableBody = document.querySelector('#datatablesSectionList tbody');
        const datatablesSectionList = document.getElementById('datatablesSectionList');

        if (!tableBody || !datatablesSectionList) {
            console.error('Table body or table element not found');
            return;
        }

        // Fetch data from Firebase
        firebase
            .database()
            .ref("user_section/")
            .on("value", function (snap) {
                const data = snap.val();
                tableBody.innerHTML = ''; // Clear existing rows

                if (data) {
                    // Iterate over each child node in the snapshot
                    Object.keys(data).forEach(key => {
                        const childData = data[key];

                        // Check if section matches the filter
                        if (sectionFilter && childData.sectionName !== sectionFilter) {
                            return; // Skip this section
                        }

                        const row = document.createElement('tr');

                        // Create view and grade buttons
                        const viewButton = `
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='StudentList.php?id=${childData.UID}&sectionName=${encodeURIComponent(childData.sectionName)}'">
                                Student List
                            </button>`;

                        // const GradeButton = `
                        //     <button class="btn btn-secondary btn-sm" onclick="window.location.href='StudentGrades.php?id=${childData.UID}&sectionName=${encodeURIComponent(childData.sectionName)}'">
                        //         Grades
                        //     </button>`;

                        row.innerHTML = `
                            <td>${childData.sectionName || 'N/A'}</td>
                            <td>
                                ${viewButton}
                            
                            </td>`;
                        tableBody.appendChild(row);
                    });

                    // Initialize or reinitialize the DataTable after adding rows
                    new simpleDatatables.DataTable(datatablesSectionList);
                    datatablesSectionList.style.display = 'table';
                }
            });
    }

    function sync() {
        var userProfileRef = firebase.database().ref("user_profile/");
        
        userProfileRef.once("value").then(function(snapshot) {
            var data = snapshot.val();
            // Send data to PHP script
            sendDataToServer(data);
        });
    }

    function sendDataToServer(data) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "FuncProfileSync.php", true); // Point to the PHP file that handles the data
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Data sent and response received');
                console.log(xhr.responseText);
            }
        };
        xhr.send(JSON.stringify(data));
    }

    function syncSection() {
        var usersectionRef = firebase.database().ref("user_section/");
        
        usersectionRef.once("value").then(function(snapshot) {
            var data = snapshot.val();
            // Send data to PHP script
            sendsectionDataToServer(data);
        });
    }
    function sendsectionDataToServer(data) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "FuncSectionSync.php", true); // Point to the PHP file that handles the data
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Data sent and response received');
                console.log(xhr.responseText);
            }
        };
        xhr.send(JSON.stringify(data));
    }

</script>
