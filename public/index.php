
<?php

// Establish a connection to the database.
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

$title = "Home";

include('../private/prepared.php');

$total_facts = count_facts();

// How many results per page?
$per_page = 8;

// How many records in total?
$total_items = count_items(); 

// So, how many pages in total?
$total_pages = ceil($total_items / $per_page);

// Make sure the page we're on exists.
$current_page = (int) ($_GET['page'] ?? 1);

if ($current_page < 1 || $current_page > $total_pages || !is_int($current_page)) {
    $current_page = 1;
}

$offset = $per_page * ($current_page - 1);

include('includes/header.php');

?>

<main class="container">
    <section class="row justify-content-center py-5 my-5">
        <div class="col-10">
        <section class="row justify-content-between">

                <!-- Introduction -->
                <div class="col-md-10 col-lg-8 col-xxl-6 mb-4">
                <h1 class="fw-light mb-5">Welcome to Mihiri's Xeno Collection</h1>
                <p class="lead">Welcome to my (and my girlfriend's) Xenosaga and Xenoblade Chronicles collection. I have been a collector of this series since 2018 and continue to collect to this day. The collectors items on this page are some of the official released items either I or my girlfriend currently own related to Xenosaga and Xenoblade Chronicles. Donations to the collection are appreciated...though probably expensive, why else would I not own it yet?</p>

                </div>
                <div
                    class="col col-lg-4 col-xxl-6 m-4 m-md-0 mb-md-4 border border-primary rounded p-3 d-flex flex-column justify-content-center align-items-center text-center">
                    <h2 class="fw-light mb-3">Fun Fact</h2>

                    <?php
                        $featuredRecord = get_random_fact($total_facts);
                        
                        if ($connection->error) {
                            echo $connection->error;
                        } elseif ($featuredRecord->num_rows > 0) {
                            $row = $featuredRecord->fetch_assoc();
                                extract($row);
                                echo "<p>$fact</p>";
                            
                        } else {
                            echo "<p>Sorry! There are no records available.</p>";
                        }
                    ?>
                </div>
            
            <hr class="my-5">

            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4 g-2" id="results">
                <?php
                
                $result = find_items($per_page, $offset);

                if ($connection->error) {
                    echo $connection->error;
                } elseif ($result->num_rows > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $filename = $row['image_filename'];
                        $item_name = $row['item_name'];
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
                                    <a href=\"item-view.php?item_name=" . urlencode($item_name) . "\" class=\"btn btn-primary mt-auto\">More Information</a>
                                </div>
                            </div>
                        </div>";
    
                    } 
                }else{
                    echo "<p>Sorry, no records available!";
                }
                
                ?>

                
            </div>
            <!-- Pagination Component -->
            <nav aria-label="Page Number">
                <ul class="pagination justify-content-center mt-2">
                    <!-- If the current page is greater than one, we'll include the previous button. -->
                    <?php if ($current_page > 1) : ?>
                        <li class="page-item">
                            <a href="index.php?page=<?php echo $current_page - 1; ?>" class="page-link">Previous</a>
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
                                    <a href="index.php?page=<?php echo $i; ?>#results" class="page-link">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php else: ?>
                                <li class="page-item">
                                    <a href="index.php?page=<?php echo $i; ?>#results" class="page-link"><?php echo $i; ?></a>
                                </li>
                            <?php endif;
                        }
                    ?>


                    <!-- If the current page is LESS THAN the total number of pages, then we'll include the 'Next' button. -->
                    <?php if ($current_page < $total_pages) : ?>
                        <li class="page-item">
                            <a href="index.php?page=<?php echo $current_page + 1; ?>" class="page-link">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </section>
</main>

<!-- BS JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
</html>

<?php

db_disconnect($connection);

?>