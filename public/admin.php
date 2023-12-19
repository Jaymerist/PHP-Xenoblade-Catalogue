<?php

// Establishing connection to the database.
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

// Importing our prepared statements.
require_once('../private/prepared.php');

$title = "Admin Home";
include("includes/header.php");

?>

<h1 class="display-3 text-center mt-5">Xeno Collection</h1>

        <p class="lead text-center">Hello, Admin. On this page, you can add, edit or delete xeno collection items from the database. 
        </p>


<?php include("includes/admin-buttons.php")?>

<p class="lead text-center">Use the buttons below to add, edit or delete fun facts.
        </p>
        <div class="text-center">
    <a href="add-fact.php" class="btn btn-success">Add</a>
    <a href="edit-fact.php" class="btn btn-warning mx-2">Edit</a>
    <a href="delete-fact.php" class="btn btn-danger">Delete</a>
</div>



<?php

include('includes/footer.php');

?>

