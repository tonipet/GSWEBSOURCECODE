<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Game Sense</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <script src="css/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                <div class="card-body">
                                    <?php
                                    // Start the session
                                    session_start();

                                    // Include the database connection
                                    include './phpConfig/config.php';

                                    // Check if there are any messages to display
                                    if (isset($_SESSION['login_message'])) {
                                        echo $_SESSION['login_message'];
                                        // Clear the message after displaying
                                        unset($_SESSION['login_message']);
                                    }
                                    ?>
                                    <form action="FuncLogin.php" method="POST">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="IdNumber" name="idnumber" type="text" required />
                                            <label for="IdNumber">ID Number</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                           
                                            <button type="submit" class="btn btn-primary">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- <div class="card-footer text-center py-3">
                                    <div class="small"><a href="register.html">Need an account? Sign up!</a></div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="css/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
