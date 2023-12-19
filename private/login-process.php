<?php

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username && $password) {

        // This query will look for the username in the database.
        $statement = $connection->prepare('SELECT * FROM catalogue_admin WHERE users = ?;');

        $statement->bind_param('s', $username);
        $statement->execute();

        $result = $statement->get_result();

        if($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['hashed_pass'])) {
                // This regenerates the session identification number to prevent session hijacking or fixation attacks. 
                session_regenerate_id();
                
                $_SESSION['username'] = $row['users'];

                $_SESSION['last_login'] = time();

                $_SESSION['login_expires'] = strtotime("+1 day midnight");

                // Redirect the user
                header("Location: admin.php");
            } else {
                $message = "<p class=\"text-warning\">Invalid username or password.</p>";
            }
        } else {
            $message = "<p class=\"text-warning\">Invalid username or password.</p>";
        }
    } else {
        $message = "<p class=\"text-warning\">Both fields are required.</p>";
    }
}
?>