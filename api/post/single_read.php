<?php
// 1. ALLOW HEADERS (Including Authorization!)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Allow GET
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. HANDLE PREFLIGHT (The Scout)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 3. Includes
include_once __DIR__ . '/../../config/db.php';

$database = new Database();
$db = $database->connect();

// 4. Get ID
if(isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    die();
}

// 5. Query
$query = 'SELECT * FROM posts WHERE id = ? LIMIT 0,1';

$stmt = $db->prepare($query);
$stmt->bindParam(1, $id);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row) {
    // 6. Create array
    $post_item = array(
        'id' => $row['id'],
        'title' => $row['title'],
        'category' => $row['category'],
        'image' => $row['image'],
        // Safety checks for null values
        'content' => html_entity_decode($row['body'] ?? $row['content'] ?? ''), 
        'author' => $row['author'],
        'date' => $row['created_at']
    );

    print_r(json_encode($post_item));
} else {
    echo json_encode(array('message' => 'Post Not Found'));
}
?>