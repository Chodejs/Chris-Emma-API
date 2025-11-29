<?php
// 1. Setup
header('Content-Type: application/json');

// CORRECTED PATH: Go UP one level (../) to find config
require_once __DIR__ . '/../config/db.php'; 

$database = new Database();
$db = $database->connect();

// 2. The Credentials we want
$username = 'Chris'; 
$raw_password = 'password123'; 

// 3. Delete old user (Cleanup)
$delete_q = "DELETE FROM users WHERE username = :username";
$del_stmt = $db->prepare($delete_q);
$del_stmt->bindParam(':username', $username);
$del_stmt->execute();

// 4. Create New User
$password_hash = password_hash($raw_password, PASSWORD_BCRYPT);

$query = "INSERT INTO users (username, password) VALUES (:username, :password)";
$stmt = $db->prepare($query);

$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password_hash);

if($stmt->execute()) {
    echo json_encode([
        "message" => "User Reset Successfully!",
        "username" => $username,
        "password" => $raw_password
    ]);
} else {
    echo json_encode(["message" => "Failed to create user."]);
}
?>