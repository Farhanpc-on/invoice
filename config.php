<?php
// Remote MySQL configuration
define('DB_HOST', 'srv605.hstgr.io');
define('DB_PORT', '3306');
define('DB_NAME', 'u272941430_client');
define('DB_USER', 'u272941430_client');
define('DB_PASS', 'B1gCl1ent123');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?>