<?php

//Initialize our SQL query and parameters
$sql = "SELECT * FROM xeno_items WHERE 1=1";
//This string will hold the data types of everything we're looking for so that we can bind our parameters to our prepared statement.
$types = "";

$per_page = 8;

// Make sure the page we're on exists.
$current_page = (int) ($_GET['page'] ?? 1);

if ($current_page < 1 || $current_page > $total_pages || !is_int($current_page)) {
    $current_page = 1;
}

$offset = $per_page * ($current_page - 1);


//This array will hold all of the values that we are going to bind
$values = [];

//This is an empty array for parameters 
$parameters = [];

$queries = []; 

// Make sure the page we're on exists.
$current_page = (int) ($_GET['page'] ?? 1);
    

foreach ($active_filters as $filter => $filter_values) {
    if (in_array($filter, ["item_type", "release_region", "favorite"])) {
        foreach ($filter_values as $value) {
            $queries[] = "$filter = ?";
            $types .= "s"; 
            $parameters[] = $value;
        }
    }
}

// Combine the queries with OR and enclose in parentheses.
if (count($queries) > 0) {
    $sql .= " AND (" . implode(" AND ", $queries) . ")";
    if ($limit > 0) {
        $sql .= " LIMIT " . $limit;
    }
    if ($offset > 0) {
        $sql .= " OFFSET ". $offset;
    }
}

//let's prepare and execute the query we built
$statement = $connection->prepare($sql);
if($statement === false){
    echo "Failed to prepare the statement: (" . $connection->errno .") ". $connection->error;
    exit();
}

$statement->bind_param($types, ...$parameters);

//if there's an error when executing the statement we'll print it out
if(!$statement-> execute()){
    echo "Execute failed: (" . $statement->errno . ")" .$statement->error;
}

$result = $statement->get_result();

if($result->num_rows > 0){
    
    $total_items = $result->num_rows;

    $total_pages = ceil($total_items / $per_page);
    
    // Make sure the page we're on exists.
    $current_page = (int) ($_GET['page'] ?? 1);
    echo "<div class=\"row\">";
    echo "<div class='row row-cols-1 row-cols-lg-2 row-cols-xl-4 g-2' id='results'>";
    
    while($row = $result->fetch_assoc()){ 
   {
    $image_filename = $row['image_filename'];
    $item_name = $row['item_name'];
    $favorite = $row['favorite'];
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
                }
                
                ?>

                
            </div>
            <!-- Pagination Component -->
            <nav aria-label="Page Number">
                <ul class="pagination justify-content-center mt-2">
                    <!-- If the current page is greater than one, we'll include the previous button. -->
                    <?php if ($current_page > 1) : ?>
                        <li class="page-item">
                            <a href="filter.php?page=<?php echo $current_page - 1; ?>" class="page-link">Previous</a>
                        </li>
                    <?php endif; 
                        // We'll use a gap instead of generating a link for every single page in the application. The gap will replace a range of page numbers with an ellipsis (...).
                        $gap = false;
                        // The window is how many pages on either side of the current page we would like to see or have generated in our loop down below.
                        $window = 1; // this is the window size
                        
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i > 1 + $window && $i < $total_pages - $window && abs($i - $current_page) > $window) {
                                if (!$gap) : ?>
                                    <li class="page-item">
                                        <span class="page-link">
                                            ...
                                        </span>
                                    </li>
                                <?php endif;

                                $gap = true;
                                continue;
                            }

                            $gap = false;

                            if ($current_page == $i) :?>
                                <li class="page-item active">
                                    <a href="filter.php?page=<?php echo $i; ?>#results" class="page-link">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php else: ?>
                                <li class="page-item">
                                    <a href="filter.php?page=<?php echo $i; ?>#results" class="page-link"><?php echo $i; ?></a>
                                </li>
                            <?php endif;
                        }
                    ?>


                    <!-- If the current page is LESS THAN the total number of pages, then we'll include the 'Next' button. -->
                    <?php if ($current_page < $total_pages) : ?>
                        <li class="page-item">
                            <a href="filter.php?page=<?php echo $current_page + 1; ?>" class="page-link">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

<?php }else{
    echo "<p>No results found.</p>";
}
echo "</div>"; //end of .row, after all the cards have been generated


?>