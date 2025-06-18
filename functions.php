<?php
// functions.php
require_once 'config.php';

function fetchRecords($table = 'invoices', $limit = 100, $orderBy = null, $orderDirection = 'ASC') {
    $conn = getDBConnection();
    if (!$conn) {
        return [];
    }

    $sql = "SELECT * FROM $table";
    if ($orderBy) {
        $sql .= " ORDER BY $orderBy $orderDirection";
    }
    $sql .= " LIMIT ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return [];
    }
    $stmt->bind_param("i", $limit);
    $stmt->execute();

    $result = $stmt->get_result();
    $records = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        $result->free(); // Free result set
    } else {
        error_log("Error fetching records from " . $table . ": " . $conn->error);
    }

    $stmt->close();
    $conn->close();
    return $records;
}

/**
 * Displays records in an HTML table.
 *
 * @param array $records The array of records to display.
 * @param array|null $columnsToDisplay An optional associative array where keys are display names and values are actual data keys.
 * If null, all columns from the first record are displayed.
 * @return string The HTML string for the table.
 */
function displayRecords($records, $columnsToDisplay = null) {
    if (empty($records)) {
        return "<p>No records found.</p>";
    }

    $html = '<div class="table-responsive"><table class="records-table">';
    $html .= '<thead><tr>';

    $actualDataKeys = [];

    // Determine headers and actual data keys
    if ($columnsToDisplay !== null && is_array($columnsToDisplay)) {
        foreach ($columnsToDisplay as $displayName => $dataKey) {
            $html .= '<th>'.htmlspecialchars($displayName).'</th>';
            $actualDataKeys[] = $dataKey;
        }
    } else {
        if (!empty($records) && isset($records[0]) && is_array($records[0])) {
            foreach (array_keys($records[0]) as $column) {
                $html .= '<th>'.htmlspecialchars(ucwords(str_replace('_', ' ', $column))).'</th>';
                $actualDataKeys[] = $column;
            }
        } else {
            error_log("Warning: Cannot determine headers for displayRecords.");
            return "<p>Error displaying records: Could not determine table headers.</p>";
        }
    }

    $html .= '</tr></thead><tbody>';

    $itemLevelKeys = [
        'description', 'quantity', 'unitPrice', 'totalAmount_item'
    ];

    // Table rows
    foreach ($records as $row) {
        $html .= '<tr>';
        foreach ($actualDataKeys as $dataKey) {
            $displayValue = '';

            if (in_array($dataKey, $itemLevelKeys) && isset($row['lineItems']) && is_array($row['lineItems'])) {
                $itemValues = [];
                $actualItemKey = ($dataKey === 'totalAmount_item') ? 'totalAmount' : $dataKey;

                foreach ($row['lineItems'] as $item) {
                    if (isset($item[$actualItemKey])) {
                        $value = $item[$actualItemKey];
                        if (is_numeric($value) && in_array($dataKey, ['quantity', 'unitPrice', 'totalAmount_item'])) {
                            $itemValues[] = number_format((float)$value, 2);
                        } else {
                            $itemValues[] = htmlspecialchars($value);
                        }
                    } else {
                        $itemValues[] = '';
                    }
                }
                $displayValue = implode('<br>', $itemValues);
            } else {
                $value = $row[$dataKey] ?? '';
                if (is_array($value) || is_object($value)) {
                    $displayValue = json_encode($value);
                } else {
                    $displayValue = $value;
                }
                $displayValue = htmlspecialchars($displayValue ?? '');
            }
            $html .= '<td>'.$displayValue.'</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</tbody></table></div>';
    return $html;
}


function getTotalRecordCount($table = 'invoices') {
    $conn = getDBConnection();
    if (!$conn) {
        return 0;
    }

    $sql = "SELECT COUNT(*) AS total FROM $table";
    $result = $conn->query($sql);
    $count = 0;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $count = $row['total'];
    }
    $conn->close();
    return $count;
}

function getLatestRecords($table = 'invoices', $limit = 5, $orderByColumn = 'created_at', $orderDirection = 'DESC') {
    $conn = getDBConnection();
    if (!$conn) {
        return [];
    }

    $table = mysqli_real_escape_string($conn, $table);
    $orderByColumn = mysqli_real_escape_string($conn, $orderByColumn);
    $orderDirection = (strtoupper($orderDirection) === 'DESC') ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM `$table` ORDER BY `$orderByColumn` $orderDirection LIMIT ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return [];
    }
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $latestRecords = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $latestRecords[] = $row;
        }
        $result->free();
    } else {
        error_log("Error getting latest records from " . $table . ": " . $conn->error);
    }
    $stmt->close();
    $conn->close();
    return $latestRecords;
}

function getTotalAmountThisMonth($table = 'invoices', $amountColumn = 'total_amount', $dateColumn = 'invoice_date') {
    $conn = getDBConnection();
    if (!$conn) {
        return 0;
    }

    $currentMonthStart = date('Y-m-01');
    $currentMonthEnd = date('Y-m-t');

    $sql = "SELECT SUM($amountColumn) AS total FROM $table WHERE $dateColumn >= ? AND $dateColumn <= ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return 0;
    }
    $stmt->bind_param("ss", $currentMonthStart, $currentMonthEnd);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalAmount = 0;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalAmount = $row['total'] ?? 0;
    }
    $stmt->close();
    $conn->close();
    return $totalAmount;
}

/**
 * Filters an array of records based on a specific column and value.
 *
 * @param array $records The array of records to filter.
 * @param string $columnName The name of the column to filter by.
 * @param mixed $filterValue The value to match in the specified column.
 * @return array The filtered array of records.
 */
function filterRecords(array $records, string $columnName, $filterValue): array
{
    $filtered = [];
    foreach ($records as $record) {
        if (isset($record[$columnName]) && $record[$columnName] == $filterValue) {
            $filtered[] = $record;
        }
    }
    return $filtered;
}

// Function to fetch data from the CloudPOS API
function fetchSalesRecordsFromAPI($accessToken) {
    $apiUrl = "https://cloudpos.bigbcomputer.com.my/connector/api/sell?";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $accessToken,
        "Content-Type: application/json",
        "Accept: application/json"
    ));
    // Added for debugging SSL issues if they arise
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        error_log("cURL Error: " . $error_msg);
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    if ($httpCode === 200) {
        return json_decode($response, true);
    } else {
        error_log("API Error: HTTP Code " . $httpCode . " Response: " . $response);
        return false;
    }
}

/**
 * Authenticates a user against the local database.
 * @param string $usernameOrEmail The username or email provided by the user.
 * @param string $password The plain-text password provided by the user.
 * @return array|null User data array on successful authentication, null on failure.
 */
function authenticateUser(string $usernameOrEmail, string $password): ?array {
    $conn = getDBConnection();
    if (!$conn) {
        error_log("Failed to get DB connection for authenticateUser.");
        return null;
    }

    // Try to find the user by email OR username (name column in your DB)
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ? OR name = ? LIMIT 1");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return null;
    }
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            unset($user['password']); // Remove hash for security
            $user['username'] = $user['name']; // Map 'name' from DB to 'username' for session consistency
            unset($user['name']); // Remove the original 'name' key
            $stmt->close();
            $conn->close();
            return $user;
        }
    }

    $stmt->close();
    $conn->close();
    return null; // Login failed (user not found or password incorrect)
}


/**
 * Gets user data by ID.
 *
 * @param int $userId The ID of the user.
 * @return array|null User data array on success, null if not found.
 */
function getUserById(int $userId): ?array {
    $conn = getDBConnection();
    if (!$conn) {
        error_log("Failed to get DB connection for getUserById.");
        return null;
    }

    $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return null;
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user;
    }

    $stmt->close();
    $conn->close();
    return null;
}

function fetchRecordsFromRemote($conn, $table) {
    // This function seems to be for switching databases on the same connection,
    // which might not be needed if you're connecting directly to 'u272941430_cloudpos'
    // in fetchRecordsFromCloudpos. Also, 'global $database;' is not good practice
    // and $database is not defined in config.php. Consider removing or redesigning.
    error_log("Warning: fetchRecordsFromRemote is called. Review its usage.");

    $originalDatabase = null;
    $currentDbQuery = $conn->query("SELECT DATABASE()");
    if ($currentDbQuery) {
        $originalDatabaseRow = $currentDbQuery->fetch_row();
        $originalDatabase = $originalDatabaseRow[0];
    }

    if (!mysqli_select_db($conn, 'u272941430_cloudpos')) {
        throw new Exception("Error selecting database: " . mysqli_error($conn));
    }

    $sql = "SELECT * FROM " . mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, $sql);

    $records = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
        mysqli_free_result($result);
    }

    // Switch back to the original database if there was one
    if ($originalDatabase && !mysqli_select_db($conn, $originalDatabase)) {
        error_log("Error switching back to original database: " . mysqli_error($conn));
    }

    return $records;
}

function fetchRecordsFromCloudpos($host, $username, $password, $table, $port) {
    // This function creates a new connection specifically for the cloudpos database.
    // This is generally a better approach than dynamically switching databases on an existing connection.
    $cloudposConn = mysqli_connect($host, $username, $password, 'u272941430_cloudpos', $port);

    if (!$cloudposConn) {
        error_log("Connection to u272941430_cloudpos failed: " . mysqli_connect_error());
        return [];
    }

    $sql = "SELECT * FROM " . mysqli_real_escape_string($cloudposConn, $table);
    $result = mysqli_query($cloudposConn, $sql);

    $records = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
        mysqli_free_result($result);
    } else {
        error_log("Error fetching from Cloudpos DB: " . mysqli_error($cloudposConn));
    }

    mysqli_close($cloudposConn);
    return $records;
}

function validateInvoiceForMyinvois(array $invoice): array {
    $errors = [];

    $requiredFields = [
        'invoiceNumber', 'invoiceDate', 'sellerId', 'buyerId',
        'buyerName', 'invoiceType', 'totalAmount',
        'currencyCode', 'invoiceStatus' // 'taxAmount' is missing in your current list, but was previously required. Adding it back if it's meant to be.
    ];

    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $invoice) || empty($invoice[$field]) && $invoice[$field] !== 0 && $invoice[$field] !== '0') { // Check for empty and 0 for numeric fields
            $errors[] = "Missing or empty field: $field";
        }
    }

    // Specific validations
    if (!isset($invoice['sellerId']) || !preg_match('/^\d{12}$/', $invoice['sellerId'])) {
        $errors[] = "Invalid sellerId: must be 12-digit TIN";
    }

    if (!isset($invoice['buyerId']) || !preg_match('/^\d{12}$/', $invoice['buyerId'])) {
        $errors[] = "Invalid buyerId: must be 12-digit TIN or NRIC";
    }

    if (!isset($invoice['invoiceType']) || !in_array($invoice['invoiceType'], ['01', '02', '03'])) {
        $errors[] = "Invalid invoiceType (expected 01, 02, or 03)";
    }

    if (!isset($invoice['currencyCode']) || !in_array($invoice['currencyCode'], ['MYR'])) {
        $errors[] = "Unsupported currencyCode (only MYR allowed)";
    }

    // Validate line items
    if (empty($invoice['lineItems']) || !is_array($invoice['lineItems'])) {
        $errors[] = "Missing or invalid lineItems array";
    } else {
        foreach ($invoice['lineItems'] as $i => $item) {
            if (!isset($item['description']) || empty($item['description'])) {
                $errors[] = "Line item #" . ($i + 1) . " missing description";
            }
            if (!isset($item['quantity']) || !is_numeric($item['quantity'])) {
                $errors[] = "Line item #" . ($i + 1) . " quantity is not numeric";
            }
            if (!isset($item['unitPrice']) || !is_numeric($item['unitPrice'])) {
                $errors[] = "Line item #" . ($i + 1) . " unitPrice is not numeric";
            }
            if (!isset($item['totalAmount']) || !is_numeric($item['totalAmount'])) {
                $errors[] = "Line item #" . ($i + 1) . " totalAmount is not numeric";
            }
        }
    }

    return $errors;
}
?>