<?php

// Establish a connection handle
require_once('/home/mkamiss1/data/connect.php');
$connection = db_connect();

$title = "Filter Xeno Collection";
include('includes/header.php');

// Here, we're going to define our filters and their options. 
$filters = [
    "item_type" => [
        'Concept Art' => 'Concept Art',
        'Figurine' => 'Figurine',
        'Standard Game' => 'Standard Game',
        'Official Soundtrack' => 'Official Soundtrack',
        'Magazine' => 'Magazine',
        'Steel Book' => 'Steel Book',
        'Other' => 'Other',
    ],
    "release_region" => [
        "North America" => "North America",
        "Japan" => "Japan",
        "Europe" => "Europe",
    ],
    "favorite" => [
        "1" => "Favorites",
        "0" => "No Favorites",
    ],
];

// Retrieve the active filters from the query string (if any).
$active_filters = [];
foreach ($_GET as $filter => $values) {
    if (!is_array($values)) {
        $values = [$values];
    }
    $active_filters[$filter] = array_map("htmlspecialchars", $values); 
}

?>

<main class="container">
    <section class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <h2 class="display-5">Filter the Data</h2>
            <p class="mb-5">Select any combination of the buttons below to filters the data.</p>

           <?php
            // Generate the filter buttons
            foreach ($filters as $filter => $options) {
                // Replace underscores or dashes with spaces and capitalise the words for the heading
                $heading = ucwords(str_replace(["_", "-"], " ", $filter));
                // Add a heading before each button group
                echo "<h3 class=\"fw-light\">" .
                    htmlspecialchars($heading) .
                    "</h3>";

                echo '<div class="btn-group mb-3" role="group" aria-label="' .
                    htmlspecialchars($filter) .
                    ' Filter Group">';
                foreach ($options as $value => $label) {
                    $is_active = in_array(
                        $value,
                        $active_filters[$filter] ?? []
                    );
                    $updated_filters = $active_filters;

                    if ($is_active) {
                        $updated_filters[$filter] = array_diff(
                            $updated_filters[$filter],
                            [$value]
                        );
                        if (empty($updated_filters[$filter])) {
                            unset($updated_filters[$filter]);
                        }
                    } else {
                        $updated_filters[$filter][] = $value;
                    }

                    $url =
                        $_SERVER["PHP_SELF"] .
                        "?" .
                        http_build_query($updated_filters);
                    echo '<a href="' .
                        htmlspecialchars($url) .
                        '" class="btn ' .
                        ($is_active ? "btn-primary" : "btn-outline-primary") .
                        '">' .
                        htmlspecialchars($label) .
                        "</a>";
                } // end of inner foreach
                echo "</div>";
            }
            // Check if any filters are active; if they are, spit out the results! 
            if (!empty($active_filters)) : ?>
                <hr>
                <div class="row">
                    <?php include("includes/filter_results.php"); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php

include('includes/footer.php');

db_disconnect($connection);

?>