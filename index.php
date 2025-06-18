<?php
session_start(); // Start the session at the very beginning of the file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'functions.php'; // Ensure functions.php is included

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : 'Guest'; // Default to 'Guest' if not logged in

// Your existing dashboard data fetching (keep these as they are)
$totalRecords = getTotalRecordCount();
$latestInvoices = getLatestRecords('invoices', 5);
$totalAmountThisMonth = getTotalAmountThisMonth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

        /* Activity Card */
        .activity-card {
            grid-column: span 2;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--gray-light);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .activity-date {
            font-size: 0.8rem;
            color: var(--gray);
        }

        /* Circular Progress */
        .circular-progress {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 20px auto;
        }

        .circular-progress svg {
            width: 100%;
            height: 100%;
        }

        .circular-progress circle {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        .circular-progress-bg {
            stroke: var(--gray-light);
        }

        .circular-progress-fill {
            stroke: var(--primary);
            stroke-dasharray: 283;
            stroke-dashoffset: calc(283 - (283 * var(--progress)) / 100);
            transition: stroke-dashoffset 0.8s ease;
        }

        .progress-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .activity-card {
                grid-column: span 1;
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
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Invoice Manager</h2>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li>
                        <a href="index.php" class="active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="remote_records.php" class="active">
                            <i class="fas fa-file-invoice"></i>
                            <span>Remote Database</span>
                        </a>
                    </li>
                    <li>
                        <a href="add_invoice.php" class="active">
                            <i class="fas fa-file-invoice"></i>
                            <span>MyInvois</span>
                        </a>
                    </li>
                    <li>
                        <a href="reports.php">
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

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <h1>Dashboard Overview</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&amp;background=4361ee&amp;color=fff" alt="User">
                    <span><?php echo htmlspecialchars($username); ?></span>
                    <?php if ($loggedIn): ?>
                        <a href="logout.php" class="profile-link">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="profile-link">Login</a>
                        <a href="register.php" class="profile-link">Register</a>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Dashboard Widgets -->
            <div class="dashboard-grid">
                <!-- Total Records Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Total Records</h3>
                        <div class="card-icon primary">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-value"><?php echo $totalRecords; ?></div>
                    </div>
                    <div class="card-footer">
                        <i class="fas fa-arrow-up text-success"></i> 5% from last month
                    </div>
                </div>

                <!-- Total Amount Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Total Amount</h3>
                        <div class="card-icon success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-value">$<?php echo number_format($totalAmountThisMonth, 2); ?></div>
                    </div>
                    <div class="card-footer">
                        <i class="fas fa-arrow-up text-success"></i> 12% from last month
                    </div>
                </div>

                <!-- Performance Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monthly Goal</h3>
                        <div class="card-icon warning">
                            <i class="fas fa-bullseye"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="circular-progress">
                            <svg viewBox="0 0 100 100">
                                <circle class="circular-progress-bg" cx="50" cy="50" r="45" />
                                <circle class="circular-progress-fill" cx="50" cy="50" r="45" 
                                    style="--progress: <?php echo min(100, ($totalAmountThisMonth / 10000) * 100); ?>" />
                            </svg>
                            <div class="progress-value"><?php echo min(100, round(($totalAmountThisMonth / 10000) * 100)); ?>%</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php echo round(($totalAmountThisMonth / 10000) * 100); ?>% of $10,000 goal
                    </div>
                </div>

                <!-- Latest Activity Card -->
                <div class="card activity-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Invoices</h3>
                        <div class="card-icon danger">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="activity-list">
                            <?php if (!empty($latestInvoices)): ?>
                                <?php foreach ($latestInvoices as $invoice): ?>
                                    <li class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
                                            <div class="activity-date">Created on <?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?></div>
                                        </div>
                                        <div class="activity-amount">
                                            $<?php echo isset($invoice['total_amount']) ? number_format($invoice['total_amount'], 2) : '0.00'; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="activity-item">
                                    <div class="activity-content">
                                        <div class="activity-title">No recent invoices found</div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="reports.php" class="text-primary">View all invoices</a>
                    </div>
                </div>
            </div>

            <!-- Additional Content -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 15px;">
                        <button style="padding: 10px 15px; background-color: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer;">
                            <i class="fas fa-plus"></i> Create Invoice
                        </button>
                        <button style="padding: 10px 15px; background-color: var(--success); color: white; border: none; border-radius: 8px; cursor: pointer;">
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                        <button style="padding: 10px 15px; background-color: var(--warning); color: white; border: none; border-radius: 8px; cursor: pointer;">
                            <i class="fas fa-download"></i> Export Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>