<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h1 class="mt-4"></h1>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-1"></i> Section List</span>
        <a href="SectionAddEdit.php" class="btn btn-primary">Add Section</a>
    </div>
    <div class="card-body">
    <table id="datatablesSectionList" style="display: none;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Section Name</th>
            <th>Description</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>ID</th>
            <th>Section Name</th>
            <th>Description</th>
        </tr>
    </tfoot>
    <tbody></tbody>
</table>

    </div>
</div>

<?php include 'layout/footer.php'; ?>


<script type="text/javascript">


    document.addEventListener('DOMContentLoaded', function() {
        GetSectionList();
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
 

});

function GetSectionList() {
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
          const row = document.createElement('tr');

          // Create edit and delete buttons with FontAwesome icons
          const editButton = `
            <button class="btn btn-primary btn-sm" onclick="window.location.href='SectionAddEdit.php?id=${childData.UID}'">
              <i class="fas fa-pencil-alt"></i>
            </button>`;
          const deleteButton = `
            <button class="btn btn-danger btn-sm delete-btn" data-id="${childData.UID}">
              <i class="fas fa-trash"></i>
            </button>`;

            row.innerHTML = `
            <td>${childData.sectionName || 'N/A'}</td>
            <td>${childData.sectionDescription || 'N/A'}</td>
            
            <td>
            ${editButton}
            ${deleteButton}
            </td>`;

          tableBody.appendChild(row);
        });

        // Initialize or reinitialize the DataTable after adding rows
        new simpleDatatables.DataTable(datatablesSectionList);

        datatablesSectionList.style.display = 'table';
      }
    });
}

</script>