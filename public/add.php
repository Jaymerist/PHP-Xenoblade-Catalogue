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

$title = "Add an item";

// Initialise all of our variables.
$message = $message ?? '';
$item_name = $_POST['item_name'] ?? '';
$item_description = $_POST['item_description'] ?? '';
$item_type = $_POST['item_type'] ?? '';
$collectors_set = $_POST['collectors_set'] ?? '';
$buy_price = $_POST['buy_price'] ?? '';
$sell_price = $_POST['sell_price'] ?? '';
$retired = $_POST['retired'] ?? '';
$release_region = $_POST['release_region'] ?? '';
$release_year = $_POST['release_year'] ?? '';
$favorite_item = $_POST['favorite_item'] ?? '';

// Form Handling
if (isset($_POST['submit'])) {
    $message = '';
    extract($_POST);

    $do_i_proceed = TRUE;

 
    // item name
    $item_name = filter_var($item_name, FILTER_SANITIZE_STRING);

    if (strlen($item_name) > 50 || strlen($item_name) < 3 || !isset($_POST['item_name'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter the name of the product. (3-50 characaters).</p>";
    } elseif (str_contains($item_name, "'") || str_contains($item_name, '"')) {
        $item_name = mysqli_real_escape_string($connection, $item_name);
    }

    // Description
    if (strlen($item_description) > 255 || strlen($item_description) < 10 || !isset($_POST['item_description'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter a description between 10-200 characters long.</p>";
    } elseif (str_contains($item_description, "'") || str_contains($item_description, '"')) {
        $item_description = mysqli_real_escape_string($connection, $item_description);
    }

    
    //Item type (dropdown)

    // Collectors Set
    if (strlen($collectors_set) > 50 || strlen($collectors_set) < 1 || !isset($_POST['collectors_set'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Please enter a collectors set name for the item (1-30 characters).</p>";
    } elseif (str_contains($collectors_set, "'") || str_contains($collectors_set, '"')) {
        $collectors_set = mysqli_real_escape_string($connection, $collectors_set);
    }

    //Buy Price

    if ($buy_price < 0 || !is_numeric($buy_price) || !isset($_POST['buy_price'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Cost must be a positive number.</p>";
    }

    //Sell Price

    if ($sell_price < 0 || !is_numeric($sell_price) || !isset($_POST['sell_price'])) {
        $do_i_proceed = FALSE;
        $message .= "<p>Cost must be a positive number.</p>";
    }

    //retired
    if (!isset($_POST['retired']) || !isset($_POST['retired'])){
        $do_i_proceed = FALSE;
        $message .= "<p>Please choose an option</p>";
    }

    //release region (dropdown)

    //favorite
    if (!isset($_POST['favorite_item']) || !isset($_POST['favorite_item'])){
        $do_i_proceed = FALSE;
        $message .= "<p>Please choose an option</p>";
    }

    //img

    // Submit
    if ($do_i_proceed == TRUE) {
        // This script will process the files.
        include('includes/upload.php');
        if($alert != ""){
            $_POST = array();

        }
    } else {
        $message = '<p>There was a problem: ' . $message . '</p>';
    }
}

include('includes/header.php');
include('includes/admin-buttons.php');

?>
    <main class="container">
        <section class="row justify-content-center py-5 my-5">
            <div class="col-6">
                <h1 class="fw-light mb-5">Upload New Collection Item</h1>

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
                    <!-- Item name -->
                    <div class="mb-3">
                        <label for="item_name">Item Name</label>
                        <input type="text" id="item_name" name="item_name" maxlength="50" class="form-control" value="<?php echo isset($_POST['item_name']) ? htmlspecialchars($_POST['item_name']) : ''; ?>" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="item_description">Description</label>
                        <input type="text" id="item_description" name="item_description" maxlength="255" class="form-control" value="<?php echo isset($_POST['item_description']) ? htmlspecialchars($_POST['item_description']) : '';?>" required>
                    </div>

                    <!-- Item Type -->
                    <div class="mb-3">
                        <label for="item_type">Item Type</label>
                        <select name="item_type" id="item_type" class="form-select form-select-lg">
                            <?php
                            $item_type_list= [
                                'Concept Art' => 'Concept Art',
                                'Figurine' => 'Figurine',
                                'Standard Game' => 'Standard Game',
                                'Official Soundtrack' => 'Official Soundtrack',
                                'Magazine' => 'Magazine',
                                'Steel Book' => 'Steel Book',
                                'Other' => 'Other',
                            ];

                            foreach ($item_type_list as $key => $value) {
                                $selected = isset($_POST['item_type']) && $_POST['item_type'] == $key ? 'selected' : '';
                                echo "<option value=\"$key\" $selected>$value</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Set -->
                    <div class="mb-3">
                        <label for="collectors_set">Collectors Set (leave as None if not applicable)</label>
                        <input type="text" id="collectors_set" name="collectors_set" maxlength="255" class="form-control" value="<?php echo isset($_POST['collectors_set']) ? htmlspecialchars($_POST['collectors_set']) : 'None'?>" required>
                    </div>

                    <!-- Buy Price -->
                    <div class="mb-3">
                        <label for="buy_price">What price did you buy this item for?</label>
                        <input type="number" id="buy_price" name="buy_price" class="form-control" value="<?php echo isset($_POST['buy_price']) ? htmlspecialchars($_POST['buy_price']) : ''; ?>">
                    </div>

                    <!-- Sell Price -->
                    <div class="mb-3">
                        <label for="sell_price">What (average) price does this item sell for?</label>
                        <input type="number" id="sell_price" name="sell_price" class="form-control" value="<?php echo isset($_POST['sell_price']) ? htmlspecialchars($_POST['sell_price']) : ''; ?>">
                    </div>

                    <!-- Retired Product -->
                    <div class="mb-3">
                        <label>Is this product retired (no chance of rerelease)?</label>
                        <div class="flex">
                            <input type="radio" id="is_retired" name="retired" value="is_retired" <?php echo (isset($_POST['retired']) && $_POST['retired'] == 'is_retired') ? 'checked' : ''; ?>>
                            <label for="is_retired">Yes</label>
                            <input type="radio" id="not_retired" name="retired" value="not_retired" <?php echo (isset($_POST['retired']) && $_POST['retired'] == 'not_retired') ? 'checked' : ''; ?>>
                            <label for="not_retired">No</label>
                        </div>
                    </div>

                    <!-- Release Year -->
                    <div class="mb-3">
                        <label for="release_year">What year was this product released?</label>
                        <input type="number" min="1998" max="2100" id="release_year" name="release_year" class="form-control" value="<?php echo isset($_POST['release_year']) ? htmlspecialchars($_POST['release_year']) : ''; ?>">
                    </div>

                    <!-- Release Region  -->
                    <div class="mb-3">
                        <label for="release_region">What is the region of origin for this product?</label>
                        <select name="release_region" id="release_region" class="form-select form-select-lg">
                            <?php
                            $region_list= [
                                'North America' => 'North America',
                                'Japan' => 'Japan',
                                'Europe' => 'Europe',
                            ];

                            foreach ($region_list as $key => $value) {
                                $selected = isset($_POST['release_region']) && $_POST['release_region'] == $key ? 'selected' : '';
                                echo "<option value=\"$key\" $selected>$value</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Favorite -->
                    <div class="mb-3">
                        <label>Favorite this item: </label>
                        <div class="flex">
                            <input type="radio" id="is_favorite_item" name="favorite_item" value="is_favorite_item" <?php echo (isset($_POST['favorite_item']) && $_POST['favorite_item'] == 'is_favorite_item') ? 'checked' : ''; ?>>
                            <label for="is_favorite_item">Yes</label>
                            <input type="radio" id="not_favorite_item" name="favorite_item" value="not_favorite_item" <?php echo (isset($_POST['favorite_item']) && $_POST['favorite_item'] == 'not_favorite_item') ? 'checked' : ''; ?>>
                            <label for="not_favorite_item">No</label>
                        </div>
                    </div>
                    <!-- File Upload -->
                    <div class="mb-3">
                        <label for="img-file">Image File</label>
                        <input type="file" id="img-file" name="img-file" class="form-control" accept=".jpg, .jpeg, .png, .webp" required>
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