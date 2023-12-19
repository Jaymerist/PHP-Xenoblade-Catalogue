<?php

if (isset($_POST['submit']) && !empty($_FILES['img-file']['name'])) {

    $file = $_FILES['img-file'];
    $file_name = $_FILES['img-file']['name'];
    $file_temp_name = $_FILES['img-file']['tmp_name'];
    $file_size = $_FILES['img-file']['size'];
    $file_error = $_FILES['img-file']['error'];
    $message = $message ?? '';
    $item_name = $_POST['item_name'];
    $item_description = $_POST['item_description'];
    $item_type = $_POST['item_type'];
    $collectors_set = $_POST['collectors_set'];
    $buy_price = $_POST['buy_price'];
    $sell_price = $_POST['sell_price'];
    $retired = $_POST['retired'];
    $release_region = $_POST['release_region'];
    $release_year = $_POST['release_year'];
    $favorite_item = $_POST['favorite_item'];

    if($retired == 'is_retired'){
        $retired = 1;
    }else{
        $retired = 0;
    }

    if($favorite_item == 'not_favorite_item'){
        $favorite_item = 0;
    }else{
        $favorite_item = 1;
    }


    // Let's grab the uploaded file's extension.
    $file_extension = explode('.', $file_name);
    $file_extension = strtolower(end($file_extension));

    $allowed = array('jpg', 'jpeg', 'png', 'webp');


    if (in_array($file_extension, $allowed) && $file_error === 0 && $file_size < 2500000) {
        $file_name_new = uniqid('', true) . "." . $file_extension;
        $file_destination = 'images/full/' . $file_name_new; 

        // Check to see if the directory exists; if not, make it.
        if (!is_dir('images/full/')) {
            mkdir('images/full/', 0777, true);
        } 
        if (!is_dir('images/thumbs')) {
            mkdir('images/thumbs/', 0777, true);
        }

        // Check if the file already exists
        if (!file_exists($file_destination)) {
            // Move the uploaded file to the directory.
            move_uploaded_file($file_temp_name, $file_destination);

            // Check the image dimensions. 
            list($width_original, $height_original) = getimagesize($file_destination);

            // Creates an empty canvas that is 256px x 256px.
            $thumb = imagecreatetruecolor(256, 256);

            // Calculate the shorter side / smaller size between width and height.
            $smaller_size = min($width_original, $height_original);
            
            // Calculate the starting point for cropping the image.
            $x_coordinate = ($width_original > $smaller_size) ? ($width_original - $smaller_size) / 2 : 0;
            $y_coordinate = ($height_original > $smaller_size) ? ($height_original - $smaller_size) / 2 : 0;

            // Create image based on the filetype we grabbed earlier.
            switch ($file_extension) {
                case 'jpeg':
                case 'jpg':
                    $src_image = imagecreatefromjpeg($file_destination);
                    break;
                case 'png':
                    $src_image = imagecreatefrompng($file_destination);
                    break;
                case 'webp':
                    $src_image = imagecreatefromwebp($file_destination);
                    break;
                default:
                    // Invalid Type
                    $message .= "<p>This file type is not supported.</p>";
                    exit;
            }

            // Crop and resize the user-uploaded image.
            imagecopyresampled($thumb, $src_image, 0, 0, $x_coordinate, $y_coordinate, 256, 256, $smaller_size, $smaller_size);
            // Save the thumbnail to the server.
            imagejpeg($thumb, 'images/thumbs/' . $file_name_new, 100);

            // Free up some server resources by destroying the working object.
            imagedestroy($thumb);
            imagedestroy($src_image);
            
            // Insert the image metadata to the database.
            $sql = "INSERT INTO xeno_items (image_filename, item_name, item_type, description, collectors_set, buy_price, sell_price, retired, release_year, release_region, favorite, uploaded_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  NOW())";
            $statement = $connection->prepare($sql);

            $statement->bind_param("sssssddiisi", $file_name_new, $item_name, $item_type, $item_description, $collectors_set, $buy_price, $sell_price, $retired, $release_year, $release_region, $favorite_item);

            try{
                $statement->execute();   
            }catch(Exception $e){
                echo $e;
            }
            

            
            $alert .= "<p>Item added successfully!</p>";
        } else {
           $message .= "<p>Could not add to collection.</p>";
        }
    } else {
        $message .= "<p>Image is too big!</p>";
    }
}else{
    $message .= "Please add an image";
}

?>