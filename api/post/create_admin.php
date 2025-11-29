<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../config/db.php';

// CHANGE THESE TO YOUR DESIRED CREDENTIALS
$username = 'Chris'; 
$password = 'password123'; // Change this!

// 1. Connect to DB
$database = new Database();
$db = $database->connect();

// 2. Hash the password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// 3. Insert User
$query = "INSERT INTO users (username, password) VALUES (:username, :password)";
$stmt = $db->prepare($query);

$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password_hash);

if($stmt->execute()) {
    echo json_encode(["message" => "Admin User Created! Now delete this file."]);
} else {
    echo json_encode(["message" => "User could not be created."]);
}
?>