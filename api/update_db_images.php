<?php
header('Content-Type: text/html');
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->connect();

echo "<h2>Database Image Column Upgrade</h2>";

// Change 'image' column to TEXT to hold JSON arrays
$sql = "ALTER TABLE posts MODIFY image TEXT";

try {
    $db->query($sql);
    echo "<h3 style='color:green'>✅ Success! 'image' column is now TEXT type.</h3>";
} catch (PDOException $e) {
    echo "<h3 style='color:red'>❌ Error: " . $e->getMessage() . "</h3>";
}
?>