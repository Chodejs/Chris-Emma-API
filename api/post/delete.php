<?php
// 1. Headers & Security
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__ . '/../../config/db.php';
include_once __DIR__ . '/../../models/Post.php';

// ---------------------------------------------------------
// CONNECT FIRST! (The Horse)
// ---------------------------------------------------------
$database = new Database();
$db = $database->connect();
// ---------------------------------------------------------

// ---------------------------------------------------------
// THEN CHECK SECURITY (The Cart)
// ---------------------------------------------------------
// Now auth_check.php can use the $db variable we just made.
require_once __DIR__ . '/../auth_check.php'; 

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Set ID to update
if(!empty($data->id)) {
    
    // Create Query
    $query = 'DELETE FROM posts WHERE id = :id';

    // Prepare statement
    $stmt = $db->prepare($query);

    // Clean Data
    $id = htmlspecialchars(strip_tags($data->id));

    // Bind Data
    $stmt->bindParam(':id', $id);

    // Execute query
    if($stmt->execute()) {
        echo json_encode(array('message' => 'Post Deleted'));
    } else {
        echo json_encode(array('message' => 'Post Not Deleted'));
    }
} else {
    echo json_encode(array('message' => 'No ID Found'));
}
?>