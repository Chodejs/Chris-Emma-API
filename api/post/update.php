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
// CONNECT
// ---------------------------------------------------------
$database = new Database();
$db = $database->connect();
// ---------------------------------------------------------

// ---------------------------------------------------------
// SECURITY CHECK
// ---------------------------------------------------------
require_once __DIR__ . '/../auth_check.php'; 

$data = json_decode(file_get_contents("php://input"));

// Make sure ID exists
if(!empty($data->id) && !empty($data->title) && !empty($data->content)) {

    // Update Query
    $query = 'UPDATE posts SET 
        title = :title, 
        category = :category, 
        image = :image, 
        excerpt = :excerpt, 
        body = :body, 
        author = :author
        WHERE id = :id';

    $stmt = $db->prepare($query);

    // Clean Data
    $title = htmlspecialchars(strip_tags($data->title));
    $category = htmlspecialchars(strip_tags($data->category));
    
    // FIX: Do not sanitize image! Keep the JSON quotes intact.
    $image = $data->image; 
    
    $author = htmlspecialchars(strip_tags($data->author));
    $id = htmlspecialchars(strip_tags($data->id));
    
    // Create new excerpt if content changed
    $excerpt = substr(strip_tags($data->content), 0, 150) . '...';

    // Bind Data
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':image', $image);
    $stmt->bindParam(':excerpt', $excerpt);
    $stmt->bindParam(':body', $data->content); 
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':id', $id);

    if($stmt->execute()) {
        echo json_encode(array('message' => 'Post Updated'));
    } else {
        http_response_code(503);
        echo json_encode(array('message' => 'Post Not Updated'));
    }
} else {
    http_response_code(400);
    echo json_encode(array('message' => 'Missing ID or Data'));
}
?>