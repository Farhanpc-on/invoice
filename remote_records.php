<?php
require_once 'functions.php';

// Initialize variables
$host = '';
$port = '3306';
$username = '';
$password = '';
$database = '';
$tableName = '';
$errorMessage = '';
$records = [];
$message = ''; // For displaying success messages

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = $_POST['host'];
    $port = $_POST['port'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $database = $_POST['database'];
    $tableName = $_POST['table_name'];

    try {
        $conn = mysqli_connect($host, $username, $password, '', $port);

        if (!$conn) {
            throw new Exception("Connection failed: " . mysqli_connect_error());
        }

        $message = "Connection successful!"; // Set success message

        // Select Database
        if (!empty($database)) {
            if (mysqli_select_db($conn, $database)) {
                $message .= "<br>Database selected: " . htmlspecialchars($database); // Append to message

                // Fetch Records (if table name is provided)
                if (!empty($tableName)) {
                    $records = fetchRecordsFromRemote($conn, $tableName);
                }

            } else {
                throw new Exception("Error selecting database: " . mysqli_error($conn));
            }
        } else {
            throw new Exception("Please enter a database name.");
        }

        mysqli_close($conn);

    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remote Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --dark: #212529;
            --light: #f8f9fa;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --white: #ffffff;
            --sidebar-width: 240px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--white);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--gray-light);
            margin-bottom: 20px;
        }

        .sidebar-header h2 {
            color: var(--primary);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .sidebar-menu {
            padding: 0 15px;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--gray);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* Form Styles */
        .remote-connection-form {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--gray-light);
            border-radius: 4px;
        }

        .form-group button {
            background-color: var(--primary);
            color: var(--white);
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group button:hover {
            background-color: var(--secondary);
        }

        .error-message {
            color: var(--danger);
            margin-top: 10px;
        }

        .success-message {
            color: var(--success);
            margin-top: 10px;
        }


        /* Table Styles */
        .data-table-section {
            margin-top: 20px;
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .data-table-section h3 {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
        }

        .records-table th,
        .records-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--gray-light);
        }

        .records-table th {
            background-color: var(--gray-light);
            font-weight: 600;
        }

        .records-table tr:hover {
            background-color: var(--light);
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Invoice Manager</h2>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li>
                        <a href="index.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="reports.php">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li>
                        <a href="remote_records.php" class="active">
                            <i class="fas fa-database"></i>
                            <span>Remote Records</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">
            <header class="header">
                <h1>Remote Database Records</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4361ee&color=fff" alt="User">
                    <span>Admin</span>
                </div>
            </header>

            <section class="remote-connection-form">
                <h2>Connect to Remote MySQL Database</h2>
                <?php if ($errorMessage): ?>
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                <?php endif; ?>
                <?php if ($message): ?>
                    <p class="success-message"><?php echo $message; ?></p>
                <?php endif; ?>

                <form method="post">
                    <div class="form-group">
                        <label for="host">Hostname / IP Address:</label>
                        <input type="text" id="host" name="host"
                            value="<?php echo htmlspecialchars($host); ?>" required>
                        <small>Enter srv605.hstgr.io or 217.21.74.101</small>
                    </div>
                    <div class="form-group">
                        <label for="port">Port:</label>
                        <input type="number" id="port" name="port"
                            value="<?php echo htmlspecialchars($port); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username"
                            value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" value="">
                    </div>
                    <div class="form-group">
                        <label for="database">Database:</label>
                        <input type="text" id="database" name="database"
                            value="<?php echo htmlspecialchars($database); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="table_name">Table Name:</label>
                        <input type="text" id="table_name" name="table_name"
                            value="<?php echo htmlspecialchars($tableName); ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit">Connect and Fetch Records</button>
                    </div>
                </form>
            </section>

            <?php if (!empty($records)): ?>
                <section class="data-table-section">
                    <h3>Records from <?php echo htmlspecialchars($tableName); ?> in <?php echo htmlspecialchars($database); ?></h3>
                    <div class="table-responsive">
                        <?php echo displayRecords($records); ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>