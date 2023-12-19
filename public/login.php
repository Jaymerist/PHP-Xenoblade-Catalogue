<?php

// Establishing connection to the database.
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

// We need to define a unique title for this page.
$title = "Login";
include("includes/header.php");

// Because we're loggin in, we need this script.
include('../private/login-process.php');

// We'll initialise this variable right away to avoid any issues.
$message = "";

// If the user is already logged in, kick them out! They shouldn't be logging in again.
if (isset($_SESSION['username'])) {
    header('Location: admin.php');
    exit();
}

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-6">
            <h2>Login</h2>
            <p class="lead">To access the administrtive area for this application, please login down below.</p>

            <?php if ($message != NULL): ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="form-group mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <input type="submit" class="btn btn-primary mt-4" id="login" name="login" value="Login">
            </form>
        </div> <!-- end of column -->

    </div> <!-- end of row -->
</div> <!-- end of container -->
</body>

</html>