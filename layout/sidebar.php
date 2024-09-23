

<?php
// Assuming you have a session variable or a database call to get the current user's role
$userRole = $_SESSION['UserType']; // or however you store the user's role

function isAdmin($role) {
    return $role === 'admin';
}
?>

<div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                        <?php if (isAdmin($userRole)): ?>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Administrator
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="Section.php">Section</a>
                                
                                </nav>
                            </div>

                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="AdminAddUser.php">Add User</a>
                                
                                </nav>
                            </div>
                          
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="AdminSectionStatistics.php">Section Statistics</a>
                                
                                </nav>
                            </div>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="AdminStudentGradesStatics.php">Grades Statistics</a>
                                
                                </nav>
                            </div>


                            <?php endif; ?>

                            </div>
                            <div class="nav">
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#UserMenu" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Menu
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="UserMenu" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="AdminSectionList.php">Student</a>
                                
                                </nav>
                            </div>

                            </div>
                    </div>


                    
                    <div class="sb-sidenav-footer">
                       GAME SENSE
                    </div>

                    
                </nav>
            </div>

            <div id="layoutSidenav_content">
            <div class="container-fluid px-4">
            <h1 class="mt-4"></h1>
            <div id="notificationSuccess" class="alert alert-primary d-none" role="alert">
            </div>