<?php
// 1. Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 2. Includes
include_once __DIR__ . '/../../config/db.php';
include_once __DIR__ . '/../../models/Post.php';

// 3. Setup
$database = new Database();
$db = $database->connect();
$post = new Post($db);

// 4. Query
$result = $post->read();
$num = $result->rowCount();

if($num > 0) {
    $posts_arr = array();
    
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        // SAFETY CHECKS: Ensure variables are strings, never null
        // We check for 'body' first (new name), then 'content' (old name fallback)
        $body_content = $body ?? $content ?? "";
        $safe_body = (string)$body_content;

        $safe_title = isset($title) ? (string)$title : "Untitled";
        $safe_author = isset($author) ? (string)$author : "Unknown";
        $safe_category = isset($category) ? (string)$category : "General";
        $safe_image = isset($image) ? (string)$image : "";
        $safe_date = isset($created_at) ? (string)$created_at : "";

        // FIX: Decode HTML entities first so we don't see &nbsp; in excerpts
        $cleanQP_text = strip_tags(html_entity_decode($safe_body));
        $excerpt = substr($cleanQP_text, 0, 120) . '...';

        $post_item = array(
            'id' => $id,
            'title' => $safe_title,
            'content' => html_entity_decode($safe_body),
            'excerpt' => $excerpt, 
            'author' => $safe_author,
            'category' => $safe_category,
            'image' => $safe_image,
            'date' => $safe_date
        );

        array_push($posts_arr, $post_item);
    }

    echo json_encode($posts_arr);

} else {
    echo json_encode([]);
}
?>