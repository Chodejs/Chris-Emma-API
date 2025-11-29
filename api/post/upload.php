<?php
// 1. Resolve Paths safely using __DIR__
// __DIR__ is "path/to/api/post"
// so __DIR__ . '/../config/db.php' becomes "path/to/api/config/database.php"

include_once __DIR__ . '/../../config/db.php';
$database = new Database();
$db = $database->connect();

// 2. Include the Gatekeeper
// This sets the CORS headers. If this file isn't found, the script will crash here (which is good for security).
require_once __DIR__ . '/../auth_check.php'; 

// ---------------------------------------------------------
// FILE UPLOAD LOGIC
// ---------------------------------------------------------
if(isset($_FILES['image'])) {
    
    $errors = array();
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    
    // Get extension safely
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $extensions = array("jpeg", "jpg", "png", "webp");

    if(in_array($file_ext, $extensions) === false){
        $errors[] = "Extension not allowed, please choose a JPEG, PNG, or WEBP file.";
    }
    
    if($file_size > 15728640) { // 5MB
        $errors[] = 'File size must be exactly 5 MB or less';
    }
    
    if(empty($errors) == true) {
        $new_name = uniqid('img_', true) . '.' . $file_ext;
        
        // Target: Go up two levels to root, then into uploads
        $target_dir = __DIR__ . "/../../uploads/"; 
        $target_file = $target_dir . $new_name;

        // Ensure upload directory exists
        if (!is_dir($target_dir)) {
             mkdir($target_dir, 0777, true);
        }

        if(move_uploaded_file($file_tmp, $target_file)) {
            // SUCCESS!
            $public_url = "http://localhost/chris-emma-api/uploads/" . $new_name;
            
            echo json_encode(array(
                "message" => "Image uploaded successfully", 
                "url" => $public_url
            ));
        } else {
             http_response_code(500);
             echo json_encode(array("message" => "Failed to move file. Permissions issue?"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => $errors[0]));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "No file selected."));
}
?>