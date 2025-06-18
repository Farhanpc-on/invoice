<?php
require_once 'config.php';
require_once 'functions.php';

// Set the header content type to JSON
header('Content-Type: application/json');

// Define the table name
$table = 'sales';

// Get the parameters from the request
$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : null;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$order_by_date = isset($_GET['order_by_date']) ? $_GET['order_by_date'] : 'DESC'; // Default order

// Validate the order direction
if (strtoupper($order_by_date) != 'ASC' && strtoupper($order_by_date) != 'DESC') {
    $order_by_date = 'DESC'; // Default
}

// Construct the query.  Using a prepared statement is crucial for security.
$sql = "SELECT * FROM $table WHERE 1=1"; // Start with a benign condition

// Add conditions based on parameters
if ($location_id !== null) {
    $sql .= " AND location_id = ?";
}
if ($start_date !== null) {
    $sql .= " AND sale_date >= ?";
}
if ($end_date !== null) {
    $sql .= " AND sale_date <= ?";
}

// Order by date
$sql .= " ORDER BY sale_date $order_by_date";

// Prepare the statement
$conn = getDBConnection(); // Get the database connection
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Handle error.  For a production API, log the error and return a generic message.
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}

// Bind parameters dynamically
$types = ""; // String to hold the types of parameters
$params = []; // Array to hold the parameter values

if ($location_id !== null) {
    $types .= "i"; // Integer
    $params[] = $location_id;
}
if ($start_date !== null) {
    $types .= "s"; // String (date)
    $params[] = $start_date;
}
if ($end_date !== null) {
    $types .= "s"; // String (date)
    $params[] = $end_date;
}

// Bind the parameters.  This is the key to preventing SQL injection.
if (count($params) > 0) {
    // Use call_user_func_array to pass the parameters dynamically
    $stmt->bind_param($types, ...$params);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $stmt->error]);
    exit;
}

// Fetch the records
$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}

// Return the records as JSON
echo json_encode($records);

// Close the statement and connection
$stmt->close();
$conn->close();
?>
