<?php
// 1. HEADERS (Must allow OPTIONS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // <--- Added OPTIONS
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. HANDLE THE SCOUT (Preflight)
// If the browser is just asking "Is it safe?", we say "YES" (200 OK) and stop here.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 3. Include DB
include_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->connect();

// 4. Get Data
$data = json_decode(file_get_contents("php://input"));

// 5. Check Logic
if(!empty($data->username) && !empty($data->password)) {

    $query = "SELECT id, username, password FROM users WHERE username = :username LIMIT 0,1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $data->username);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(password_verify($data->password, $row['password'])) {
            
            // Generate & Save Token
            $token = bin2hex(random_bytes(32)); 
            
            $update_query = "UPDATE users SET token = :token WHERE id = :id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':token', $token);
            $update_stmt->bindParam(':id', $row['id']);
            
            if($update_stmt->execute()) {
                echo json_encode(array(
                    "message" => "Successful login.",
                    "user" => array("id" => $row['id'], "username" => $row['username']),
                    "token" => $token
                ));
            } else {
                 http_response_code(500);
                 echo json_encode(array("message" => "Login failed during token save."));
            }

        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Invalid Password."));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "User Not Found."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data. Send username and password."));
}
?>