<?php
header('Content-Type: text/html');
require_once __DIR__ . '/../config/db.php';

echo "<h2>Database Repair Tool</h2>";

$database = new Database();
$db = $database->connect();

// 1. Get Current Columns
echo "Checking 'posts' table...<br>";
$query = "DESCRIBE posts";
$stmt = $db->prepare($query);
$stmt->execute();
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Found columns: " . implode(", ", $columns) . "<br><br>";

// 2. The Logic
if (in_array('body', $columns)) {
    echo "<span style='color:green'>✅ Column 'body' already exists. You are good!</span>";
} elseif (in_array('content', $columns)) {
    // If you named it 'content', let's rename it to 'body' to match our code
    echo "Found column 'content' instead of 'body'. Renaming...<br>";
    $sql = "ALTER TABLE posts CHANGE content body TEXT NOT NULL";
    if ($db->query($sql)) {
        echo "<span style='color:green'>✅ SUCCESS: Renamed 'content' to 'body'.</span>";
    } else {
        echo "<span style='color:red'>❌ FAILED to rename column.</span>";
    }
} else {
    // If it's missing entirely, add it
    echo "Column 'body' is MISSING. Adding it now...<br>";
    $sql = "ALTER TABLE posts ADD body TEXT NOT NULL AFTER title";
    if ($db->query($sql)) {
        echo "<span style='color:green'>✅ SUCCESS: Added column 'body'.</span>";
    } else {
        echo "<span style='color:red'>❌ FAILED to add column.</span>";
    }
}
?>