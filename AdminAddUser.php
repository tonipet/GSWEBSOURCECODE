<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>
<?php 
$db = getDB();
?>


<div class="card mb-4">
<?php
      if (isset($_SESSION['reset_message'])) {
          echo $_SESSION['reset_message'];
          unset($_SESSION['reset_message']);
      }
      ?>

    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-1" ></i></span>
        <a href="AdminSaveEditUser.php" class="btn btn-success">Add New User</a>
    </div>
    <div class="card-body">
        <table id="datatablesSimple" >
            <thead>
                <tr>
                    <th>FullName</th>
                    <th>EmailAddress</th>
                    <th>IDNumber</th>
                    <!-- <th>Password</th> -->
                    <th>Usertype</th>
                    <th>Section</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                 <th>FullName</th>
                 <th>EmailAddress</th>
                    <th>IDNumber</th>
                    <!-- <th>Password</th> -->
                    <th>Usertype</th>
                    <th>Section</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </tfoot>
            <tbody>
            <?php
                // Example of fetching data and displaying it
                if ($db) {
                    $query = "SELECT UserId,FullName, IDNumber, EmailAddress,Password, Usertype, Active,Section FROM tblusers";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($results as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['FullName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['EmailAddress']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['IDNumber']) . "</td>";
                        // echo "<td>" . htmlspecialchars($row['Password']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Usertype']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Section']) . "</td>";
                        echo "<td>" . ($row['Active'] == 1 ? 'Active' : 'Not Active') . "</td>";
                        echo "<td>
                        <a href='AdminSaveEditUser.php?id=" . urlencode($row['UserId']) . "' class='btn btn-info'>Edit</a>
                        <a href='FuncResetPassword.php?id=" . urlencode($row['UserId']) . "' class='btn btn-info'>Reset Password</a>
                      </td>";
                        echo "</tr>";
                    }
                }
                ?>


            </tbody>
        </table>
    </div>
</div>



<?php include 'layout/footer.php'; ?>