<?php
// 1. HEADERS & CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. HANDLE PREFLIGHT
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ---------------------------------------------------------
// TOKEN HUNTING
// ---------------------------------------------------------
$token = null;
$debug_source = "None";

// Check Apache Headers
if (function_exists('apache_request_headers')) {
    $requestHeaders = apache_request_headers();
    $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
    if (isset($requestHeaders['Authorization'])) {
        $headers = trim($requestHeaders['Authorization']);
        $debug_source = "Apache Headers";
    }
}

// Check Server Variables (The .htaccess fix puts it here)
if (empty($headers)) {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) { 
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        $debug_source = "HTTP_AUTHORIZATION";
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { 
        $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        $debug_source = "REDIRECT_HTTP_AUTHORIZATION";
    }
}

// Extract Bearer
if (!empty($headers)) {
    if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        $token = $matches[1];
    }
}

// ---------------------------------------------------------
// DIAGNOSTICS (If it fails)
// ---------------------------------------------------------

if(!$token) {
    http_response_code(401);
    echo json_encode(array(
        "message" => "Unauthorized. Token missing.",
        "debug_info" => array(
            "source_found" => $debug_source,
            "received_headers" => $headers ?? "NULL",
            "fix_suggestion" => "Ensure .htaccess is in /api/ folder"
        )
    ));
    exit(); 
}

// ---------------------------------------------------------
// DB VERIFICATION
// ---------------------------------------------------------

// Check DB
$auth_query = "SELECT id FROM users WHERE token = :token LIMIT 0,1";
$auth_stmt = $db->prepare($auth_query);
$auth_stmt->bindParam(':token', $token);
$auth_stmt->execute();

if($auth_stmt->rowCount() == 0) {
    http_response_code(401);
    echo json_encode(array(
        "message" => "Unauthorized. Invalid token.",
        "debug_info" => array(
            "token_received" => $token,
            "db_check" => "Token not found in users table. Try logging out and back in."
        )
    ));
    exit(); 
}

// If we survive this, the user is legit!
?>