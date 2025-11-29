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

$post = new Post($db);

// 2. Get Raw Data
$data = json_decode(file_get_contents("php://input"));

// 3. Create
if(
    !empty($data->title) &&
    !empty($data->content)
) {
    $post->title = $data->title;
    $post->body = $data->content;
    $post->author = $data->author ?? 'Chris'; 
    $post->category = $data->category;
    $post->image = $data->image;

    if($post->create()) {
        echo json_encode(array("message" => "Post Created"));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create post."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete Data."));
}
?>