<?php
header('Content-Type: text/html'); // Pure HTML for easy reading

$host = 'localhost';
$username = 'root';
$password = 'mysql'; // Testing your password
$db_name = 'chris_emma_blog'; // Testing your DB Name

echo "<h2>Database Connection Test</h2>";
echo "Target: $db_name @ $host<br>";
echo "User: $username<br>";
echo "Pass: $password<br><hr>";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h3 style='color:green'>✅ CONNECTED SUCCESSFULLY!</h3>";
    
    // Check for tables
    echo "<b>Checking Tables:</b><br>";
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<span style='color:orange'>Warning: Database exists but is EMPTY (No tables found).</span>";
    } else {
        echo "Found tables: " . implode(", ", $tables);
    }

} catch(PDOException $e) {
    echo "<h3 style='color:red'>❌ CONNECTION FAILED</h3>";
    echo "<b>Error Message:</b> " . $e->getMessage() . "<br><br>";
    echo "<b>Troubleshooting:</b><br>";
    echo "1. If error is 'Access denied', check password.<br>";
    echo "2. If error is 'Unknown database', check spelling of '$db_name'.<br>";
    echo "3. If error is 'Connection refused', MySQL might be stopped or on a different port.";
}
?>