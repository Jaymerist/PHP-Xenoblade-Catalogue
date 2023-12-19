<?php

// Establishing connection to database.
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

// Importing our prepared statements.
require_once('../private/prepared.php');

// We need to define the unique title for this page.
$title = "Edit Fun Fact";
include('includes/header.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}


include('includes/admin-buttons.php');

$fact_id = isset($_GET['id']) ? $_GET['id'] : "";
if (isset($_GET['id'])) {
    $fact_id = $_GET['id'];
} elseif (isset($_POST['id'])) {
    $fact_id = $_POST['id'];
} else {
    $fact_id = "";
}

$message = "";
$update_message = "";

$user_fact = $_POST['fact'] ? trim($_POST['fact']) : "";


if (isset($fact_id)) {
    if (is_numeric($fact_id) && $fact_id > 0) {

        $fun_fact = select_fact_by_id($fact_id);

        if ($fun_fact) {
            $existing_fact = $fun_fact['fact'];

        } else {
            $message .= "Sorry, there are no records available that match your query.";
        }
    }
}

if (isset($_POST['submit'])) {

    extract($_POST);

    $do_i_proceed = TRUE;
    
 
    // fact
    if (strlen($user_fact) > 300 || strlen($user_fact) < 10 || !isset($_POST['fact'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter a fun fact. (10-300 characaters).</p>";
    } elseif (str_contains($user_fact, "'") || str_contains($user_fact, '"')) {
        $user_fact = mysqli_real_escape_string($connection, $user_fact);
    }

    // Submit
    if ($do_i_proceed == TRUE) {
        // Call the function that does all the update stuff for us.

            update_fact($fact, $fact_id);
            $fact_id = "";
            $message = '<p class="text-success">Fact updated successfully!</p>';
       
        
        $item_id = "";
    }
}

?>

<main class="container">
<section class="row justify-content-center">

            <div class="col-10">
            <h1 class=" text-center fw-light mt-5">Edit A Record</h1>
        <p class="lead text-center">To edit a record in our database, click 'Edit' beside the row you would like to
            change. Next, add your updated values into the form and hit 'Save'.</p>
            <?php if ($message != '') : ?>
                    <div class="text-success text-center">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4 g-2">
  
            <?php 
             $query = "SELECT * FROM xeno_facts";

             $result = mysqli_query($connection, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['fid'];
                    $fact = $row['fact'];
                
                    echo "
                    <div class=\"col\">
                        <div class=\"card p-0 shadow-sm h-100\">
                            <div class=\"card-body d-flex flex-column\">
                                    <p class=\"card-text fs-4\">$fact</p>
                                <a href=\"edit-fact.php?id=" . urlencode($id) . "\" class=\"btn btn-warning mt-auto\">Edit Record</a>
                            </div>
                        </div>
                    </div>";
            }
        ?>
                </div>
            </div>
        </div>
        </div>

        <!-- Modal -->

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="exampleModalLabel">
                            Edit Fun Fact
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <?php if (isset($message)) : ?>
                    <div class="message text-danger">
                        <?php echo $message; ?>
                    </div>
                    <?php endif; ?>

                        <!-- EDIT FORM -->
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

                            <?php if (isset($update_message)): ?>
                                <div class="message text-danger">
                                    <?php echo $update_message; ?>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="fact" class="form-label">Make edits to this fun fact</label>
                                <input type="text" id="fact" name="fact" class="form-control" value="<?php
                                if ($user_fact != "") {
                                    echo $user_fact;
                                } else {
                                    echo $existing_fact;
                                }
                                ?>">
                            </div>

                            <!-- Hidden Values -->
                            <input type="hidden" name="fact_id" value="<?php echo $fact_id; ?>">

                            <!-- Submit -->
                            <input type="submit" value="Update" name="submit" class="btn btn-success">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
    crossorigin="anonymous"></script>

<?php if ($fact_id): ?>

    <script>
        var myModal = new bootstrap.Modal(document.getElementById("exampleModal"), {});

        document.onreadystatechange = function () {
            myModal.show();
        };
    </script>

<?php endif; ?>

</body>

</html>

<?php

// Close the connection.
db_disconnect($connection);

?>