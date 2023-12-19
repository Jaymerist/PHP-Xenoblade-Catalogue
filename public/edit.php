<?php

// Establishing connection to database.
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

// Importing our prepared statements.
require_once('../private/prepared.php');

// We need to define the unique title for this page.
$title = "Edit Item";
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
$update_message = "";

$user_item_name = $_POST['item_name'] ? trim($_POST['item_name']) : "";
$user_item_description = $_POST['item_description'] ? trim($_POST['item_description']) : "";
$user_item_type = $_POST['item_type'] ? trim($_POST['item_type']) : "";
$user_collectors_set = $_POST['collectors_set'] ? trim($_POST['collectors_set']) : "";
$user_buy_price = $_POST['buy_price'] ? trim($_POST['buy_price']) : "";
$user_sell_price = $_POST['sell_price'] ? trim($_POST['sell_price']) : "";
$user_retired = $_POST['retired'] ? $_POST['retired'] : "";
$user_release_region = $_POST['release_region'] ? trim($_POST['release_region']) : "";
$user_release_year = $_POST['release_year'] ? trim($_POST['release_year']) : "";
$user_favorite_item = $_POST['favorite_item'] ? $_POST['favorite_item']: "";

if (isset($item_id)) {
    if (is_numeric($item_id) && $item_id > 0) {

        $item = select_item_by_id($item_id);

        if ($item) {
            $existing_item_name = $item['item_name'];
            $existing_description = $item['description'];
            $existing_item_type = $item['item_type'];
            $existing_collectors_set = $item['collectors_set'];
            $existing_buy_price = $item['buy_price'];
            $existing_sell_price = $item['sell_price'];
            $existing_retired = $item['retired'];
            $existing_release_region = $item['release_region'];
            $existing_release_year = $item['release_year'];
            $existing_favorite_item = $item['favorite'];
        } else {
            $message .= "Sorry, there are no records available that match your query.";
        }
    }
}

if (isset($_POST['submit'])) {

    extract($_POST);

    $do_i_proceed = TRUE;
    
 
    // item name
    $item_name = filter_var($item_name, FILTER_SANITIZE_STRING);

    if (strlen($user_item_name) > 50 || strlen($user_item_name) < 3 || !isset($_POST['item_name'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter the name of the product. (3-50 characaters).</p>";
    } elseif (str_contains($user_item_name, "'") || str_contains($user_item_name, '"')) {
        $user_item_name = mysqli_real_escape_string($connection, $user_item_name);
    }

    // Description
    if (strlen($user_item_description) > 300 || strlen($user_item_description) < 10 || !isset($_POST['item_description'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter a description between 10-250 characters long.</p>";
    } elseif (str_contains($user_item_description, "'") || str_contains($user_item_description, '"')) {
        $user_item_description = mysqli_real_escape_string($connection, $user_item_description);
    }

    
    //Item type (dropdown)

    // Collectors Set
    if (strlen($user_collectors_set) > 50 || strlen($user_collectors_set) < 1 || !isset($_POST['collectors_set'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter a collectors set name for the item (1-30 characters).</p>";
    } elseif (str_contains($user_collectors_set, "'") || str_contains($user_collectors_set, '"')) {
        $user_collectors_set = mysqli_real_escape_string($connection, $user_collectors_set);
    }

    //Buy Price

    if ($user_buy_price < 0 || !is_numeric($user_buy_price) || !isset($_POST['buy_price'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Buy price must be a positive number.</p>";
    }

    //Sell Price

    if ($user_sell_price < 0 || !is_numeric($user_sell_price) || !isset($_POST['sell_price'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Sell price must be a positive number.</p>";
    }

    //retired
    if (!isset($_POST['retired']) || !isset($_POST['retired'])){
        $do_i_proceed = FALSE;
        $message .= "<p>Please choose an option for the retired section</p>";
    }

    //Year

    if ($user_release_year < 1998 || $user_release_year > 2100 || !is_numeric($user_release_year) || !isset($_POST['release_year'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Enter a valid year.</p>";
    }

    //release region (dropdown)

    //favorite
    if (!isset($_POST['favorite_item']) || !isset($_POST['favorite_item'])){
        $do_i_proceed = FALSE;
        $message .= "<p>Please choose an option for the favorite section</p>";
    }

    // Submit
    if ($do_i_proceed == TRUE) {
        // Call the function that does all the update stuff for us.
        if($user_retired == 'is_retired'){
            $user_retired = 1;
        }else{
            $user_retired = 0;
        }
    
        if($user_favorite_item == 'not_favorite_item'){
            $user_favorite_item = 0;
        }else{
            $user_favorite_item = 1;
        }
        
        try{
            update_item($user_item_name, $user_item_type, $user_item_description, $user_collectors_set, $user_buy_price, $user_sell_price, $user_retired, $user_release_year, $user_release_region, $user_favorite_item, $item_id);
            $message = '<p class="text-success">Item updated successfully!</p>';
        }catch(Exception $e){
            echo $e;
        }
        
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
                                <a href=\"edit.php?id=" . urlencode($id) . "\" class=\"btn btn-warning mt-auto\">Edit Record</a>
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
                            Edit Item
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
                                <label for="item_name" class="form-label">Item Name</label>
                                <input type="text" id="item_name" name="item_name" class="form-control" value="<?php
                                if ($user_item_name != "") {
                                    echo $user_item_name;
                                } else {
                                    echo $existing_item_name;
                                }
                                ?>">
                            </div>

                            <div class="mb-3">
                                <label for="item_description" class="form-label">Description</label>
                                <input type="text" id="item_description" name="item_description" class="form-control" value="<?php
                                if ($user_item_description != "") {
                                    echo $user_item_description;
                                } else {
                                    echo $existing_description;
                                }
                                ?>">
                            </div>

                            <div class="mb-3">
                                <label for="item_type" class="form-label">Item Type</label>
                                <select name="item_type" id="item_type" class="form-select">
                                    <?php

                                    $item_type_list = [
                                        'Concept Art' => 'Concept Art',
                                        'Figurine' => 'Figurine',
                                        'Standard Game' => 'Standard Game',
                                        'Official Soundtrack' => 'Official Soundtrack',
                                        'Magazine' => 'Magazine',
                                        'Steel Book' => 'Steel Book',
                                        'Other' => 'Other',
                                    ];

                                    foreach ($item_type_list as $key => $value) {
                                        if ($user_item_type == $key or $existing_item_type == $key) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }

                                        echo "<option value=\"$key\" $selected>$value</option>";
                                    }

                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="collectors_set" class="form-label">Collectors Set</label>
                                <input type="text" id="collectors_set" name="collectors_set" class="form-control" value="<?php
                                if ($user_collectors_set != "") {
                                    echo $user_collectors_set;
                                } else {
                                    echo $existing_collectors_set;
                                }
                                ?>">
                            </div>

                            <div class="mb-3">
                                <label for="buy_price" class="form-label">Buy Price</label>
                                <input type="number" id="buy_price" name="buy_price" class="form-control" value="<?php
                                if ($user_buy_price != "") {
                                    echo $user_buy_price;
                                } else {
                                    echo $existing_buy_price;
                                }
                                ?>">
                            </div>

                            <div class="mb-3">
                                <label for="buy_price" class="form-label">Sell Price</label>
                                <input type="number" id="sell_price" name="sell_price" class="form-control" value="<?php
                                if ($user_sell_price != "") {
                                    echo $user_sell_price;
                                } else {
                                    echo $existing_sell_price;
                                }
                                ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Retired</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="retired" id="is_retired" value="is_retired" <?php echo ($existing_retired == 1 || $user_retired == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_retired">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="retired" id="not_retired" value="not_retired" <?php echo ($existing_retired == 0 || $user_retired == 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="not_retired">
                                        No
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="release_region" class="form-label">Item Type</label>
                                <select name="release_region" id="release_region" class="form-select">
                                    <?php

                                    $release_region_list= [
                                        'North America' => 'North America',
                                        'Japan' => 'Japan',
                                        'Europe' => 'Europe',
                                    ];

                                    foreach ($release_region_list as $key => $value) {
                                        if ($user_release_region == $key or $existing_release_region == $key) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }

                                        echo "<option value=\"$key\" $selected>$value</option>";
                                    }

                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="release_year" class="form-label">Release Year</label>
                                <input type="number" id="release_year" name="release_year" class="form-control" value="<?php
                                if ($user_release_year != "") {
                                    echo $user_release_year;
                                } else {
                                    echo $existing_release_year;
                                }
                                ?>">
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Favorite</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="favorite_item" id="is_favorite_item" value="is_favorite_item" <?php echo ($existing_favorite_item == 1 || $user_favorite_item == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_favorite_item">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="favorite_item" id="not_favorite_item" value="not_favorite_item" <?php echo ($existing_favorite_item == 0 || $user_favorite_item == 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="not_favorite_item">
                                        No
                                    </label>
                                </div>
                            </div>

                            <!-- Hidden Values -->
                            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">

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