<?php

// Establishing connection to database.
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

// Importing our prepared statements.
require_once('../private/prepared.php');

// We need to define the unique title for this page.
$title = "Delete an item";
include('includes/header.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}


include('includes/admin-buttons.php');

$item_id = isset($_GET['id']) ? $_GET['id'] : "";
if (isset($_GET['id'])) {
    $item_id = $_GET['id'];
} elseif (isset($_POST['id'])) {
    $item_id = $_POST['id'];
} else {
    $item_id = "";
}

$message = "";

if (isset($item_id)) {
    if (is_numeric($item_id) && $item_id > 0) {

        $item = select_item_by_id($item_id);

        if ($item) {
            $existing_item_name = $item['item_name'];
        } else {
            $message .= "Sorry, there are no records available that match your query.";
        }
    }
}

if (isset($_POST['submit'])) {

        try{
            delete_item($_POST['item_id']);
            $message = '<p class="text-success">Item was removed from database.</p>';
        }catch(Exception $e){
            echo $e;
        }
        
        $item_id = "";
    
}

?>

<main class="container">
<section class="row justify-content-center">

            
            
            <div class="col-10">
            <h1 class="fw-light text-center mt-5">Delete A Record</h1>
        <p class="lead text-center">To delete a record in our database, click 'Delete' under the item you would like to
            remove.</p>
            <?php if ($message != '') : ?>
                    <div class="text-success text-center">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4 g-2">
  
            <?php 
             $query = "SELECT * FROM xeno_items ORDER BY uploaded_on ASC;";

             $result = mysqli_query($connection, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $filename = $row['image_filename'];
                    $uploaded_on = $row['uploaded_on'];
                    $item_name = $row['item_name'];
                    $item_description = $row['description'];
                    $item_type = $row['item_type'];
                    $collectors_set = $row['collectors_set'];
                    $buy_price = $row['buy_price'];
                    $sell_price = $row['sell_price'];
                    $retired = $row['retired'];
                    $release_region = $row['release_region'];
                    $release_year = $row['release_year'];
                    $favorite = $row['favorite'];
                    echo "
                    <div class=\"col\">
                        <div class=\"card p-0 shadow-sm h-100\">
                            <img src=\"images/thumbs/$filename\" alt=\"$item_name\" class=\"card-img-top\">
                            <div class=\"card-body d-flex flex-column\">
                                ";
                            if ($favorite == 1) {
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="#ec8ebb"><path d="M2 9.1C2 14 6 16.6 9 19c1 .8 2 1.6 3 1.6s2-.8 3-1.6c3-2.3 7-4.9 7-9.8 0-4.8-5.5-8.3-10-3.6C7.5.8 2 4.3 2 9.1Z" fill="#ec8ebb"/></svg>';
                            }
                        echo "
                                <div class=\"d-flex align-items-center mb-1\">
                                    <h2 class=\"card-text fs-4\">$item_name</h2>
                                </div>
                                <a href=\"delete.php?id=" . urlencode($id) . "\" class=\"btn btn-danger mt-auto\">Delete Record</a>
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
                            Delete Confirmation
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <div class="message text-danger">
                        <p>Are you sure you want to delete <?php echo $existing_item_name ?>? This cannot be undone.</p>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <!-- Hidden Values -->
                        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                            <!-- Submit -->
                            <input type="submit" value="Delete" name="submit" class="btn btn-danger text-center">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
    crossorigin="anonymous"></script>

<?php if ($item_id): ?>

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