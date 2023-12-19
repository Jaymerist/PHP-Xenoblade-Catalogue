<?php

/*

On the server, there will also be the following directories: 

    /images
        /full
        /thumbs

*/ 

// Establish a connection to the database.
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

include('../private/prepared.php');

$title = "Add an item";

// Initialise all of our variables.
$message = $message ?? '';
$fact = $_POST['fact'] ?? '';

// Form Handling
if (isset($_POST['submit'])) {
    $message = '';
    extract($_POST);

    $do_i_proceed = TRUE;

 
    // item name
    $fact = filter_var($fact, FILTER_SANITIZE_STRING);

    if (strlen($fact) > 300 || strlen($fact) < 10 || !isset($_POST['fact'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter your fun fact etween 10 and 300 characters long.</p>";
    } elseif (str_contains($fact, "'") || str_contains($fact, '"')) {
        $fact = mysqli_real_escape_string($connection, $fact);
    }

    // Submit
    if ($do_i_proceed == TRUE) {
        add_fact($fact);
        $alert = "Fun fact added!";
        $_POST = array();
    } else {
        $message = '<p>There was a problem: ' . $message . '</p>';
    }
}

include('includes/header.php');


?>
    <main class="container">
        <section class="row justify-content-center py-5 my-5">
            <div class="col-6">
                <h1 class="fw-light mb-5">Enter new fun fact</h1>

                <!-- Error Message -->
                <?php if ($message != '') : ?>
                    <div class="alert alert-secondary my-3">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <!-- User Message -->
                <?php if (isset($error)) : ?>
                    <div class="message text-danger">
                        <?php echo $error; ?>
                    </div>
                <?php else : ?>
                    <div class="message text-success">
                        <?php echo $alert; ?>
                    </div>
                <?php endif; ?>

                <!-- Preview: if there's a newly created image, we'll show a preview of it to the user. -->

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                    <!-- Fun Fact -->
                    <div class="mb-3">
                        <label for="fact">Enter a fun fact related to Xenogears, Xenosaga or Xenoblade Chronicles</label>
                        <input type="text" id="fact" name="fact" maxlength="300" class="form-control" value="<?php echo isset($_POST['fact']) ? htmlspecialchars($_POST['fact']) : ''; ?>" required>
                    </div>

                    <!-- Submit -->
                    <div class="my-5">
                        <input type="submit" name="submit" id="submit" value="Upload" class="btn btn-primary">
                    </div>
                </form>
                </div>

            </div>
        </section>
    </main>
    
  </body>
</html>