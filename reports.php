<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'functions.php';

// IMPORTANT: Replace 'YOUR_CLOUD_POS_ACCESS_TOKEN' with your actual access token
$accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI0IiwianRpIjoiMWM1NjdiNDNkN2ZmZDkwYzdiMzA2YzY2NjI2NDZiMmNhYjMwODk5YTlkOTY3MjE2ZmZkYzBhODBiZTExNzcyNGE5ZmFiNmJkNGMyMWU4MGEiLCJpYXQiOjE3NDc2NTE1MjYuMDE2NjMxLCJuYmYiOjE3NDc2NTE1MjYuMDE2NjM1LCJleHAiOjE3NzkxODc1MjUuOTkxNzksInN1YiI6IjIiLCJzY29wZXMiOltdfQ.CP5HRq9691sd733cFZaBaTb3xPbrlAyaYilal01nZw-LjocE5BbkDzzFgYjpJhNgpfca_Syxokm3MGzzcnqfY_I-hH2FBZuy_nBiGZujLQl1-01Ss75iJdT0PNSqv-rivAmBzfk1cNbZwR_9cOFKL_BdfwAwa7b8JgaqRtbNnMltJjJ2nxzCdaqW120DybP7nmQDmscVGj4x1vuSetl9rgcUtuJSbb8lLs1R-E3aybeQMx4mBiVLFWSOFAeGVwDCPcN7zOKILIV3GQt7K1dpiG_ft1xywvPogMVubXUf2cpE5cUZ2weOUsxeDnjYZF-Aq0WpVPts-fe663XZyeQ3Prv_H8Wd46Aaw0fTajyU3BBLuyENYfrwO3MUwH9rDmc3C_ZTwKhvjyPYK1QZ3oaMx8804c_XKu4q31jdDE15MkL9JdtUOIHRRcTnDDyAERb5tPW_PrF-IaaM8Qxjt0r6aKbAjfPts6tZnDB9hgfbM4zFIB-PfNijb1Gy7gdghAVd89Z6A_u8mh2b7fW2QcKZwgW33DVFakv_vq_tWN0Z208tTb2P5SypoSrE8wlRby2B6dNDkPm1HjbSndwc0XiKzke8zgqgz8XDfYoSLTCvA4Z_v7VJmsssyw0n6cUVO1Q96-NXcOdI-nMTPANyihfM4_YNKv9PJ4CufleWa9twoHI'; 

// Fetch data from CloudPOS API
$apiResponse = fetchSalesRecordsFromAPI($accessToken);

// Initialize records array
$records = []; 

// Check if API call was successful and if 'data' key exists
if ($apiResponse && isset($apiResponse['data']) && is_array($apiResponse['data'])) {
    $records = $apiResponse['data']; // Assuming the actual records are under the 'data' key
} else {
    // If API call fails or no data, log the error and ensure $records is an empty array
    error_log("Error fetching sales records from API or 'data' key not found in response: " . json_encode($apiResponse));
    // You might want to display a user-friendly message on the page here
}

// Define the columns you want to display and their corresponding data keys from the API response.
// These keys must match the exact field names returned by your CloudPOS API.
$displayColumns = [
    'Invoice Number' => 'invoiceNumber',
    'Invoice Date' => 'invoiceDate',
    'Seller ID' => 'sellerId',
    'Buyer ID' => 'buyerId',
    'Buyer Name' => 'buyerName',
    'Invoice Type' => 'invoiceType',
    // Line item details - these keys are expected within the 'lineItems' array for each item
    'Product Description' => 'description', 
    'Quantity' => 'quantity',
    'Unit Price' => 'unitPrice',
    'Line Item Total' => 'totalAmount_item', // This refers to the total amount for an individual line item
    // Main invoice totals and other top-level fields
    'Total Invoice Amount' => 'totalAmount', // This refers to the overall invoice total
    'Currency' => 'currencyCode',
    'Total Tax Amount' => 'taxAmount', // This refers to the overall tax amount
    'Invoice Status' => 'invoiceStatus'
];

// Define filter parameters for e-invoice
// Using 'invoiceStatus' as the column name for filtering
$filterColumn = 'invoiceStatus';
$filterValue = 'NEW'; // Example status. Change 'NEW' to the actual status you want to filter by (e.g., 'SUBMITTED', 'CANCELLED')

// Apply the filter
$filteredRecords = filterRecords($records, $filterColumn, $filterValue);

$totalRecords = count($filteredRecords);
$totalAmount = 0;
// Sum the 'totalAmount' for filtered records
foreach ($filteredRecords as $record) {
    if (isset($record['totalAmount']) && is_numeric($record['totalAmount'])) {
        $totalAmount += (float)$record['totalAmount'];
    } else {
        error_log("Warning: 'totalAmount' not found or not numeric for a record in filtered data.");
    }
}
$averageAmount = $totalRecords > 0 ? $totalAmount / $totalRecords : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        /* Card Styles */
        .card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 500;
            color: var(--gray);
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
        }

        .card-icon.primary {
            background-color: var(--primary);
        }

        .card-icon.success {
            background-color: var(--success);
        }

        .card-icon.warning {
            background-color: var(--warning);
        }

        .card-icon.danger {
            background-color: var(--danger);
        }

        .card-body {
            margin-bottom: 15px;
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin: 10px 0;
        }

        .card-footer {
            font-size: 0.85rem;
            color: var(--gray);
        }

        /* Table Styles */
        .data-table-section {
            margin-top: 30px;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--white);
        }

        .records-table th {
            background-color: var(--primary);
            color: var(--white);
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
        }

        .records-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--gray-light);
        }

        .records-table tr:last-child td {
            border-bottom: none;
        }

        .records-table tr:hover {
            background-color: var(--primary-light);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar-header h2, 
            .sidebar-menu a span {
                display: none;
            }
            
            .sidebar-menu a {
                justify-content: center;
                padding: 15px 0;
            }
            
            .sidebar-menu a i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .main-content {
                margin-left: 70px;
                padding: 15px;
            }
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
                        <a href="add_invoice.php" class="active">
                            <i class="fas fa-file-invoice"></i>
                            <span>MyInvois</span>
                        </a>
                    </li>
                    <li>
                        <a href="reports.php" class="active">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
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
                <h1>Sales Reports</h1> <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4361ee&color=fff" alt="User">
                    <span>Admin</span>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Total Sales Records</h3>
                        <div class="card-icon primary">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-value"><?php echo $totalRecords; ?></div>
                    </div>
                    <div class="card-footer">
                        <i class="fas fa-arrow-up text-success"></i> All time records
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Total Sales Amount</h3>
                        <div class="card-icon success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-value">$<?php echo number_format($totalAmount, 2); ?></div>
                    </div>
                    <div class="card-footer">
                        <i class="fas fa-arrow-up text-success"></i> Combined total
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Average Sales Amount</h3>
                        <div class="card-icon warning">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-value">$<?php echo number_format($averageAmount, 2); ?></div>
                    </div>
                    <div class="card-footer">
                        Average sales value
                    </div>
                </div>
            </div>

            <section class="data-table-section">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sales Data</h3>
                        <div class="card-icon danger">
                            <i class="fas fa-table"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php 
                            if ($records !== false) { // This check was for the previous error, but still relevant.
                                echo displayRecords($records); 
                            } else {
                                echo "<p>Error fetching sales records from API. Please check your access token and API connectivity.</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        Showing <?php echo count($records); ?> records
                    </div>
                </div>
            </section>

            <section class="export-section">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Export Data</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; gap: 15px;">
                            <button style="padding: 10px 15px; background-color: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer;">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                            <button style="padding: 10px 15px; background-color: var(--success); color: white; border: none; border-radius: 8px; cursor: pointer;">
                                <i class="fas fa-file-pdf"></i> Export to PDF
                            </button>
                            <button style="padding: 10px 15px; background-color: var(--warning); color: white; border: none; border-radius: 8px; cursor: pointer;">
                                <i class="fas fa-file-csv"></i> Export to CSV
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>