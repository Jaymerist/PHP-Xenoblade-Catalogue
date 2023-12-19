<?php

// Establish a connection handle
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

// Retrieve the comic name from the query string.
$item_name = $_GET['item_name'] ? $_GET['item_name'] : "No Item";

$title = $item_name;

include('includes/header.php');

?>

<main class="container flex-column d-flex align-items-center">
    <div class="card col-md-10 col-lg-8 col-xxl-6">
        <?php
            // If there is nothing in the query string, we're not going to run the query.
            if ($item_name === "No Item") {
                echo "<h2>Oops!</h2>";
                echo "<p>Item not found. (1)</p>";
            }
            else {

                $query = "SELECT * FROM xeno_items WHERE item_name = ?;";
                $statement = mysqli_prepare($connection, $query);
                mysqli_stmt_bind_param($statement, "s", $item_name);
                mysqli_stmt_execute($statement);
                $result = mysqli_stmt_get_result($statement);

                if (!$result) {
                    die("Query failed: " . mysqli_error($connection));
                } 
                else {
                    // Fetch the comic details
                    $row = mysqli_fetch_assoc($result);

                    if (!$row) {
                        echo "<h2>Oops!</h2>";
                        echo "<p>Item not found. (2)</p>";
                    } else {
                        
                        ?>

                        <!-- Card Output -->
                        <div class="card px-0 mx-auto">
                            <!-- Card Header -->
                            <div class="card-header text-bg-dark">
                                <h3 class="card-item_name fw-light fs-5">
                                    <?php echo $item_name; ?>
                                </h3>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body text-center">
                                <img class="full-image" src="images/full/<?php echo $row['image_filename'] ?>" alt="image of <?php echo $item_name?>">
                                <p class="card-text">
                                    <?php echo $row['item_type'] ?>  |  Released in <?php echo $row['release_year'] ?>  |  <?php if($row['retired'] == 1){ echo "Retired Item";}else{echo "Available / Chance for Rerelease";} ?>
                                </p>
                                <p class="card-text">
                                    This copy was released in <?php echo $row['release_region']; ?>
                                </p>
                                <p class="card-text">
                                    <?php echo $row['description']; ?>
                                </p>

                                <p class="card-text">
                                    Bought for: $<?php echo $row['buy_price']; ?> USD  |  Average price: $<?php echo $row['sell_price']?> USD
                                </p>

                                <p class="card-text">
                                    <?php if($row['collectors_set'] != "None"){
                                        echo "This item is part of the " . $row['collectors_set'] . " collectors set.";
                                    }else{
                                        echo "This item is not part of a collectors set.";
                                    } ?>
                                </p>
                            </div>
                        </div>


                        <?php
                    }
                }
            }
        ?>
    </div>
</main>

<?php

include('includes/footer.php');

?>