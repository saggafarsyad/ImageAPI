<?php

define("API_KEY", "test");

function generateResponse($status, $name = "-", $size = 0)
{
    return array(
        "status" => $status,
        "name" => $name,
        "size" => $size
    );
}

if (isset($_POST['k'])) {
    if (strcmp($_POST['k'], API_KEY) == 0) {
        $result = array();

        $target_dir = "uploads/";
        $target_filename = basename($_FILES["image_file"]["name"]);
        $target_file = $target_dir . $target_filename;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image_file"]["tmp_name"]);
        if ($check !== false) {
            // Check if file already exists
            if (file_exists($target_file)) {
                $result = generateResponse("Failed: Already Exist");
            } else {
                // Check file size
                $file_size = $_FILES["image_file"]["size"];

                if ($file_size > 500000) {
                    $result = generateResponse("Failed: Too Large");
                } else {
                    // Allow certain file formats
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                        && $imageFileType != "gif"
                    ) {
                        $result = generateResponse("Failed: Type Not Allowed");
                    } else {
                        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
                            $result = generateResponse("Success", $target_filename, $file_size);
                        } else {
                            $result = generateResponse("Failed: An Error Occurred");
                        }
                    }
                }
            }
        } else {
            $result = generateResponse("Failed");
        }


    } else {
        $result = generateResponse("Access Denied");
    }
} else {
    $result = generateResponse("Unauthorized");
}

header("Content-Type: application/json");
echo json_encode($result);

?>