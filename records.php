<?php
require_once 'config.php';
require_once 'functions.php';

// Get records from database
$conn = getDBConnection();
$table = isset($_GET['table']) ? $_GET['table'] : 'invoices';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM $table";
if ($search) {
    $countQuery .= " WHERE invoice_number LIKE '%$search%' OR client_name LIKE '%$search%'";
}
$countResult = $conn->query($countQuery);
$totalRecords = $countResult->fetch_assoc()['total'];
$recordsPerPage = 10;
$totalPages = ceil($totalRecords / $recordsPerPage);
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

// Get records with pagination
$query = "SELECT * FROM $table";
if ($search) {
    $query .= " WHERE invoice_number LIKE '%$search%' OR client_name LIKE '%$search%'";
}
$query .= " LIMIT $offset, $recordsPerPage";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Records - Invoice Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional styles specific to records page */
        .records-container {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-top: 20px;
        }
        
        .records-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .records-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .search-box {
            position: relative;
            width: 300px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--gray-light);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .records-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .records-table th {
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
        }
        
        .records-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--gray-light);
            vertical-align: middle;
        }
        
        .records-table tr:last-child td {
            border-bottom: none;
        }
        
        .records-table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status.paid {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .status.pending {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning);
        }
        
        .status.overdue {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger);
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            background-color: transparent;
            border: none;
            color: var(--gray);
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 5px;
        }
        
        .action-btn:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .pagination a, 
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            margin: 0 5px;
            color: var(--gray);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        
        .pagination .active {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .no-records {
            text-align: center;
            padding: 50px;
            color: var(--gray);
        }
        
        .table-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
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
                        <a href="index.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="records.php" class="active">
                            <i class="fas fa-file-invoice"></i>
                            <span>Invoices</span>
                        </a>
                    </li>
                    <li>
                        <a href="remote_records.php" class="active">
                            <i class="fas fa-file-invoice"></i>
                            <span>Invoices</span>
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
                <h1>Manage Invoices</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4361ee&color=fff" alt="User">
                    <span>Admin</span>
                </div>
            </header>

            <!-- Records Container -->
            <div class="records-container">
                <div class="table-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <form action="records.php" method="get">
                            <input type="text" name="search" placeholder="Search invoices..." value="<?php echo htmlspecialchars($search); ?>">
                            <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
                        </form>
                    </div>
                    <button class="btn-primary" onclick="window.location.href='add_invoice.php'">
                        <i class="fas fa-plus"></i> New Invoice
                    </button>
                </div>
                
                <div class="table-responsive">
                    <?php if ($result->num_rows > 0): ?>
                        <table class="records-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['invoice_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['invoice_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['due_date'])); ?></td>
                                        <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <?php 
                                                $status = strtolower($row['status'] ?? 'pending');
                                                $statusClass = 'pending';
                                                if ($status === 'paid') $statusClass = 'paid';
                                                if ($status === 'overdue') $statusClass = 'overdue';
                                            ?>
                                            <span class="status <?php echo $statusClass; ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="action-btn" title="View" onclick="window.location.href='view_invoice.php?id=<?php echo $row['id']; ?>'">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn" title="Edit" onclick="window.location.href='edit_invoice.php?id=<?php echo $row['id']; ?>'">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn" title="Delete" onclick="if(confirm('Are you sure you want to delete this invoice?')) { window.location.href='delete_invoice.php?id=<?php echo $row['id']; ?>'; }">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-records">
                            <i class="fas fa-file-invoice-dollar" style="font-size: 3rem; margin-bottom: 15px;"></i>
                            <h3>No invoices found</h3>
                            <p>Create your first invoice by clicking the "New Invoice" button</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="records.php?table=<?php echo $table; ?>&page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $currentPage): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="records.php?table=<?php echo $table; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="records.php?table=<?php echo $table; ?>&page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>