<?php

// Prepared statement for updating a attraction
$update_statement = $connection->prepare("UPDATE xeno_items SET item_name = ?, item_type = ?, description = ?, collectors_set = ?, buy_price = ?, sell_price = ?, retired = ?, release_year = ?, release_region = ?, favorite = ? WHERE id = ?;");

// Prepared statement for deleting a attraction
$delete_statement = $connection->prepare("DELETE FROM xeno_items WHERE id = ?;");

// Prepared statement for selecting a specific attraction (on edit page)
$specific_select_statement = $connection->prepare("SELECT * FROM xeno_items WHERE id = ?;");

// Function to handle database errors
function handle_database_error($statement) {
    global $connection;
    die("Error in: " . $statement . ". Error details: " . $connection->error);
}

function total_search_items($search){
    global $connection;
    $sql = "SELECT COUNT(*) FROM xeno_items
    WHERE
        item_name LIKE CONCAT('%', ?, '%') OR
        description LIKE CONCAT('%', ?, '%') OR
        item_type LIKE CONCAT('%', ?, '%') OR
        collectors_set LIKE CONCAT('%', ?, '%') OR
        release_region LIKE CONCAT('%', ?, '%')";
    $statement = $connection->prepare($sql);
    $statement->bind_param("sssss", $search, $search, $search, $search, $search);
    $statement->execute();
    $result = $statement->get_result();
    $count = $result->fetch_row()[0];
    
    return $count;
    }
function simple_search($search, $limit, $offset){
    global $connection;
    //I am not adding prices, or boolean columns to this search since that's a bit impractical 
    $sql = "SELECT * FROM xeno_items
                        WHERE
                            item_name LIKE CONCAT('%', ?, '%') OR
                            description LIKE CONCAT('%', ?, '%') OR
                            item_type LIKE CONCAT('%', ?, '%') OR
                            collectors_set LIKE CONCAT('%', ?, '%') OR
                            release_region LIKE CONCAT('%', ?, '%')";
                if ($limit > 0) {
                    $sql .= " LIMIT " . $limit;
                }
                if ($offset > 0) {
                    $sql .= " OFFSET ". $offset;
                }
                $statement = $connection->prepare($sql);
                $statement->bind_param("sssss", $search, $search, $search, $search, $search);
                $statement->execute();
                $result = $statement->get_result();
                return $result;
}

//count records in items table
function count_items() {
    global $connection;
    $sql = "SELECT COUNT(*) FROM xeno_items;";
    $results = mysqli_query($connection, $sql);
    $count = mysqli_fetch_row($results);
    return $count[0];
}

function find_items($limit=0, $offset= 0) {
    global $connection;
    $sql = "SELECT item_name, image_filename, favorite FROM xeno_items";
    if ($limit > 0) {
        $sql .= " LIMIT " . $limit;
    }
    if ($offset > 0) {
        $sql .= " OFFSET ". $offset;
    }
    $result = $connection->query($sql);
    return $result;
}

function advanced_search($item_name, $item_type, $collectors_set, $buy_min, $buy_max, $sell_min, $sell_max, $retired, $year_min, $year_max, $release_region, $favorite){

    global $connection;
    
    $query = "SELECT item_name, image_filename FROM xeno_items WHERE 1 = 1";
            
                $parameters = [];
                
                $types= '';

                    if (!empty($item_name)) {
                        $query .= " AND item_name LIKE CONCAT('%', ?, '%')";
                        $parameters[] = $item_name;
                        $types .= 's';
                    }

                    if (!empty($item_type)) {
                        $query .= " AND item_type LIKE CONCAT('%', ?, '%')";
                        $parameters[] = $item_type;
                        $types .= 's';
                    }

                    if (!empty($collectors_set)) {
                        $query .= " AND collectors_set LIKE CONCAT('%', ?, '%')";
                        $parameters[] = $collectors_set;
                        $types .= 's';
                    }

                    if (!empty($release_region)) {
                        $query .= " AND release_region LIKE CONCAT('%', ?, '%')";
                        $parameters[] = $release_region;
                        $types .= 's';
                    }

                    if (!empty($buy_min) && !empty($buy_max)) {
                        $query .= " AND buy_price BETWEEN ? AND ?";
                        $parameters[] = $buy_min;
                        $parameters[] = $buy_max;
                        $types .= 'dd';
                    }
                    
                    if (!empty($sell_min) && !empty($sell_max)) {
                        $query .= " AND sell_price BETWEEN ? AND ?";
                        $parameters[] = $sell_min;
                        $parameters[] = $sell_max;
                        $types .= 'dd';
                    }
                    
                    if($retired == 0 || $retired == 1){
                        $query .= " AND retired = ?";
                        $parameters[] = $retired;
                        $types .= 'i';
                    }
                    
                    if($favorite == 0 || $favorite == 1){
                        $query .= " AND favorite = ?";
                        $parameters[] = $favorite;
                        $types .= 'i';
                    }
                    
                    if (!empty($publisher_search)) {
                        $query .= " AND publisher LIKE CONCAT('%', ?, '%')";
                        $parameters[] = $publisher_search;
                        $types .= 's';
                    }

                    if (!empty($year_min) && !empty($year_max)) {
                        $query .= " AND year BETWEEN $year_min AND $year_max";
                    }

                    $statement = $connection->prepare($query);
                        if ($types) {
                            $statement->bind_param($types, ...$parameters);
                        }
    
                        $statement->execute();
                        $result = $statement->get_result();

        return $result;
    }


// Function to retrieve a specific item, by its ID
function select_item_by_id($id) {
    global $connection;
    global $specific_select_statement;

    $specific_select_statement->bind_param("i", $id);

    if (!$specific_select_statement->execute()) {
        handle_database_error("selecting item by ID");
    }

    $result = $specific_select_statement->get_result();
    $specific_item = $result->fetch_assoc();
    
    return $specific_item;
}

// Function to update an existing item
function update_item($item_name, $item_type, $description, $collectors_set, $buy_price, $sell_price, $retired, $release_year, $release_region, $favorite, $id) {
    
    global $connection;
    global $update_statement;
    
    $update_statement->bind_param("ssssddiisii", $item_name, $item_type, $description, $collectors_set, $buy_price, $sell_price, $retired, $release_year, $release_region, $favorite, $id);
    
    try{

        $update_statement->execute();
    }catch(Exception $e){
        echo $e;
    }
    
}

// Function to delete a specific item by primary key
function delete_item($id) {
    global $connection;
    global $delete_statement;

    $delete_statement->bind_param("i", $id);

    try{
        $delete_statement->execute();
    }catch(Exception $e){
        echo $e;
    }
}

//Function to get a random fact

function get_random_fact($numOfRecords){
    global $connection;

    $randomRecord = rand(1,$numOfRecords);

    $sql = "SELECT fact FROM xeno_facts WHERE fid=" . $randomRecord;
    $result = $connection->query($sql);

    return $result;
}

//count number of facts
function count_facts() {
    global $connection;
    $sql = "SELECT COUNT(*) FROM xeno_facts;";
    $results = mysqli_query($connection, $sql);
    $count = mysqli_fetch_row($results);
    return $count[0];
}

//add new fact
function add_fact($fun_fact){
global $connection;
$sql = $connection->prepare("INSERT INTO xeno_facts(fact) VALUES (?)");

$sql->bind_param("s", $fun_fact);
$sql->execute();
}

//edit fact

function update_fact($fact, $fact_id) {
    
    global $connection;
    $sql = $connection->prepare("UPDATE xeno_facts SET fact = ? WHERE fid = ?");
    
    $sql->bind_param("si", $fact, $fact_id);
    
    $sql->execute();

    
}

//delete fact

function delete_fact($fact_id) {
    
    global $connection;
    $sql = $connection->prepare("DELETE FROM xeno_facts WHERE fid = ?");
    
    $sql->bind_param("i", $fact_id);
    
    $sql->execute();

    
}

//Select fact by id

function select_fact_by_id($fid) {
    global $connection;
    $sql = $connection->prepare("SELECT * FROM xeno_facts WHERE fid = ?;");

    $sql->bind_param("i", $fid);

    if (!$sql->execute()) {
        handle_database_error("selecting fact by ID");
    }

    $result = $sql->get_result();
    $specific_fact = $result->fetch_assoc();
    
    return $specific_fact;
}



?>