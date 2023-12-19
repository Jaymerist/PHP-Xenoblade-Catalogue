<?php

// Establish a connection handle
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

$title = "Advanced Search: The Comic Chronicler";
include('includes/header.php');

// Includes our SQL queries and functions for pagination.
include('../private/prepared.php');

$item_name = isset($_GET['item_name']) ? $_GET['item_name'] : '';
$item_type = isset($_GET['item_type']) ? $_GET['item_type'] : '';
$release_region = isset($_GET['release_region']) ? $_GET['release_region'] : '';
$year_min = isset($_GET['year_min']) ? $_GET['year_min'] : '';
$year_max = isset($_GET['year_max']) ? $_GET['year_max'] : '';
$retired = isset($_GET['retired']) ? $_GET['retired'] : '';
$favorite = isset($_GET['favorite']) ? $_GET['favorite'] : '';
$collectors_set = isset($_GET['collectors_set']) ? $_GET['collectors_set'] : '';
$buy_min = isset($_GET['buy_min']) ? $_GET['buy-min'] : '';
$buy_max = isset($_GET['buy_max']) ? $_GET['buy_max'] : '';
$sell_min = isset($_GET['sell_min']) ? $_GET['sell_min'] : '';
$sell_max = isset($_GET['sell_max']) ? $_GET['sell_max'] : '';


?>

<main class="container">
    <section class="row justify-content-center mb-5">
        <div class="col col-md-10 col-xl-8">
            <h2 class="display-5 mb-5">Advanced Search</h2>
            <p class="mb-5">Search our data below by looking at different categories. To get started, pick the options you want and click the 
            'Search' button. This will show you the results based upon what you selected.</p>

            <form action="search.php#results" method="GET" class="mb-5 border border-success p-3 rounded">
                <!-- Name -->
                <fieldset class="my-5">
                    <legend class="fs-5">Search by Item Name</legend>
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Enter the name or keyword of the item:</label>
                        <input type="search" id="item_name" name="item_name" class="form-control" value="<?php echo isset($_GET['item_name']) ? $_GET['item_name'] : ''; ?>">
                    </div>
                </fieldset>

                <!-- Type -->
                <fieldset class="my-5">
                    <legend class="fs-5">Search by Item Type</legend>
                        <select name="item_type" id="item_type" class="form-select form-select-lg">
                            <?php
                            $item_type_list= [
                                '' => "Select...",
                                'Concept Art' => 'Concept Art',
                                'Figurine' => 'Figurine',
                                'Standard Game' => 'Standard Game',
                                'Official Soundtrack' => 'Official Soundtrack',
                                'Magazine' => 'Magazine',
                                'Steel Book' => 'Steel Book',
                                'Other' => 'Other',
                            ];

                            foreach ($item_type_list as $key => $value) {
                                $selected = isset($_GET['item_type']) && $_GET['item_type'] == $key ? 'selected' : '';
                                echo "<option value=\"$key\" $selected>$value</option>";
                            }
                            ?>
                        </select>
                </fieldset>

                <!-- Type -->
                <fieldset class="my-5">
                    <legend class="fs-5">Search by Collectors Set Name</legend>
                    <div class="mb-3">
                        <label for="collectors_set" class="form-label">Enter the name of the standard game for which the set belongs to:</label>
                        <input type="search" id="collectors_set" name="collectors_set" class="form-control" value="<?php echo isset($_GET['collectors_set']) ? $_GET['collectors_set'] : ''; ?>">
                    </div>
                </fieldset>

                <!-- Region -->
                <fieldset class="my-5">
                    <legend class="fs-5">Search by Region</legend>
                    <label for="release_region">Enter the region of origin for this product:</label>
                        <select name="release_region" id="release_region" class="form-select form-select-lg">
                            <?php
                            $region_list= [
                                '' => "Select...",
                                'North America' => 'North America',
                                'Japan' => 'Japan',
                                'Europe' => 'Europe',
                            ];

                            foreach ($region_list as $key => $value) {
                                $selected = isset($_GET['release_region']) && $_GET['release_region'] == $key ? 'selected' : '';
                                echo "<option value=\"$key\" $selected>$value</option>";
                            }
                            ?>
                        </select>
                </fieldset>

                <!--Year-->

                <fieldset class="my-5">
                    <legend class="fs-5">Release Year Range</legend>

                    <div class="mb-3">
                        <label for="year_min" class="form-label">Show results starting at year:</label>
                        <input type="number" min="1997" max="2100" class="form-control" id="year_min" name="year_min" value="<?php echo $year_min; ?>">

                    </div>

                    <div class="mb-3">
                        <label for="year_max" class="form-label">and ending in year:</label>
                        <input type="number" min="1997" max="2100" class="form-control" id="year_max" name="year_max" value="<?php echo $year_max; ?>">

                    </div>
                </fieldset>

                <!-- Retired -->
                <fieldset class="my-5">
                    <legend class="fs-5">Sort</legend>
                    <div class="mb-3">
                        <label class="form-label">Search by production status:</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="retired" id="not-retired" value="0"
                                <?php echo (isset($_GET['retired']) && $_GET['retired'] == 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="not-retired">
                                Not Retired
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="retired" id="is-retired" value="1"
                                <?php echo (isset($_GET['retired']) && $_GET['retired'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is-retired">
                                Retired
                            </label>
                        </div>
                    </div>
                </fieldset>

                <!-- Favorite -->
                <fieldset class="my-5">
                    <legend class="fs-5">Favorite Status:</legend>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="favorite" id="not-favorite" value="0"
                                <?php echo (isset($_GET['favorite']) && $_GET['favorite'] == 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="not-favorite">
                                Not Favorite
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="favorite" id="is-favorite" value="1"
                                <?php echo (isset($_GET['favorite']) && $_GET['favorite'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is-favorite">
                                Favorite
                            </label>
                        </div>
                    </div>
                </fieldset>

                <!--Buy Range-->

                <fieldset class="my-5">
                    <legend class="fs-5">Search by buy-price range</legend>

                    <div class="mb-3">
                        <label for="buy_min" class="form-label">Show results starting at buy price:</label>
                        <input type="number" min="1997" max="2100" class="form-control" id="buy_min" name="buy_min" value="<?php echo $buy_min; ?>">

                    </div>

                    <div class="mb-3">
                        <label for="buy_max" class="form-label">and ending in price:</label>
                        <input type="number" min="1997" max="2100" class="form-control" id="buy_max" name="buy_max" value="<?php echo $buy_max; ?>">

                    </div>
                </fieldset>

                <!--Sell Range-->

                <fieldset class="my-5">
                    <legend class="fs-5">Search by sell-price range</legend>

                    <div class="mb-3">
                        <label for="sell_min" class="form-label">Show results starting at sell price:</label>
                        <input type="number" min="1997" max="2100" class="form-control" id="sell_min" name="sell_min" value="<?php echo $sell_min; ?>">

                    </div>

                    <div class="mb-3">
                        <label for="sell_max" class="form-label">and ending in price:</label>
                        <input type="number" min="1997" max="2100" class="form-control" id="sell_max" name="sell_max" value="<?php echo $sell_max; ?>">

                    </div>
                </fieldset>

                <!-- Submit -->
                <div class="my-5">
                        <input type="submit" name="submit" id="submit" value="Search" class="btn btn-primary">
                </div>
            </form>

            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4 g-2" id="results">
            <?php
                if (isset($_GET['submit'])) {

                        $result = advanced_search($item_name, $item_type, $collectors_set, $buy_min, $buy_max, $sell_min, $sell_max, $retired, $year_min, $year_max, $release_region, $favorite);
                        
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                extract($row);
                                echo "
                                <div class=\"col\">
                                    <div class=\"card p-0 shadow-sm h-100\">
                                        <img src=\"images/thumbs/$image_filename\" alt=\"$item_name\" class=\"card-img-top\">
                                        <div class=\"card-body d-flex flex-column\">
                                            ";
                                        if ($favorite == 1) {
                                            echo '<svg viewBox="0 0 24 24" fill="none" stroke="#ec8ebb"><path d="M2 9.1C2 14 6 16.6 9 19c1 .8 2 1.6 3 1.6s2-.8 3-1.6c3-2.3 7-4.9 7-9.8 0-4.8-5.5-8.3-10-3.6C7.5.8 2 4.3 2 9.1Z" fill="#ec8ebb"/></svg>';
                                        }
                                    echo "
                                            <div class=\"d-flex align-items-center mb-1\">
                                                <h2 class=\"card-text fs-4\">$item_name</h2>
                                            </div>
                                            <a href=\"item-view.php?item_name=" . urlencode($item_name) . "\" class=\"btn btn-primary mt-auto\">More Information</a>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<p>Sorry, there are no records available that match your query.</p>";
                        }
                }
        ?>
            </div>
    </section>
</main>


<?php

include('includes/footer.php');

?>