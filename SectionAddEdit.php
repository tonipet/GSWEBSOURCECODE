<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<!-- Your form HTML -->
<div class="container mt-4">
  <form id="sectionForm">
    <div class="mb-3">
      <label for="sectionName" class="form-label">Section Name</label>
      <input type="text" class="form-control" id="sectionName" name="sectionName" required>
    </div>
    <div class="mb-3">
      <label for="sectionDescription" class="form-label">Description</label>
      <textarea class="form-control" id="sectionDescription" name="sectionDescription" rows="3" required></textarea>
    </div>
    <div class="d-flex justify-content-end">
      <button type="submit" id="AddSection" class="btn btn-primary me-2" style="display: none;">Add Section</button>
      <button type="submit" id="EditSection" class="btn btn-primary me-2" style="display: none;">Save</button>
      <a href="Section.php" class="btn btn-secondary">Back to List</a>
    </div>
  </form>
</div>

<?php include 'layout/footer.php'; ?>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
  const addButton = document.getElementById("AddSection");
  const editButton = document.getElementById("EditSection");
  const sectionForm = document.getElementById("sectionForm");

  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get('id');

  if (!id) {
    showAddButton(true);
  } else {
    showEditButton(true);
    firebase.database().ref("user_section/")
      .orderByChild("UID").equalTo(id).once("value")
      .then(function(snap) {
        const data = snap.val();
        if (data) {
          // Iterate over each child node in the snapshot
          Object.keys(data).forEach(key => {
            const childData = data[key];

            // Populate the input fields with the fetched data
            document.getElementById("sectionName").value = childData.sectionName || '';
            document.getElementById("sectionDescription").value = childData.sectionDescription || '';
          });
        } else {
          console.log('No data found for the given ID');
        }
      })
      .catch(error => {
        console.error('Error fetching data:', error);
      });
  }

  sectionForm.addEventListener('submit', function(event) {
    // Prevent the default form submission behavior
    event.preventDefault();

    // Get input data
    const sectionName = document.getElementById("sectionName").value.trim();
    const sectionDescription = document.getElementById("sectionDescription").value.trim();

    // If data is null, return early (validation failed)
    if (!sectionName || !sectionDescription) {
      return;
    }

    if (addButton && addButton === event.submitter) {
      firebase.database().ref("user_section/").push({
        sectionName: sectionName,
        sectionDescription: sectionDescription,
        UID: Date.now().toString()
      })
      .then(() => {
        showNotification("Successfully Inserted", "primary");
        document.getElementById("sectionName").value = '';
        document.getElementById("sectionDescription").value = '';
      })
      .catch(error => {
        console.error('Error inserting data:', error);
      });
    } else if (editButton && editButton === event.submitter) {
      firebase.database().ref("user_section/")
        .orderByChild("UID").equalTo(id).once("value")
        .then(snap => {
          const data = snap.val();
          if (data) {
            const updateKey = Object.keys(data)[0];
            return firebase.database().ref("user_section/" + updateKey).update({
              sectionName: sectionName,
              sectionDescription: sectionDescription
            });
          } else {
            console.log('No data found for the given ID');
          }
        })
        .then(() => {
          showNotification("Successfully Updated", "primary");
        })
        .catch(error => {
          console.error('Error updating data:', error);
        });
    }
  });

  function showEditButton(shouldShow) {
    if (editButton) {
      editButton.style.display = shouldShow ? 'inline-block' : 'none';
    }
  }

  function showAddButton(shouldShow) {
    if (addButton) {
      addButton.style.display = shouldShow ? 'inline-block' : 'none';
    }
  }
});
</script>
