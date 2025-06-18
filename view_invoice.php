<?php
require_once 'config.php';

// Get invoice ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch invoice data
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$invoice) {
    header("Location: records.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoice - Invoice Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .invoice-container {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-top: 20px;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .invoice-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .invoice-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .invoice-status.paid {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .invoice-status.pending {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning);
        }
        
        .invoice-status.overdue {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger);
        }
        
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .invoice-section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 1rem;
            font-weight: 500;
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .section-content {
            font-size: 1rem;
            color: var(--dark);
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table th {
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
        }
        
        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .text-right {
            text-align: right;
        }
        
        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 10px 15px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .totals-table tr:last-child td {
            font-weight: 600;
            border-bottom: none;
        }
        
        .invoice-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background-color: var(--primary-light);
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
                        <a href="records.php">
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
                <h1>Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4361ee&color=fff" alt="User">
                    <span>Admin</span>
                </div>
            </header>

            <!-- Invoice Container -->
            <div class="invoice-container">
                <div class="invoice-header">
                    <h2 class="invoice-title">Invoice Details</h2>
                    <span class="invoice-status <?php echo strtolower($invoice['status']); ?>">
                        <?php echo ucfirst($invoice['status']); ?>
                    </span>
                </div>

                <div class="invoice-details">
                    <div>
                        <div class="invoice-section">
                            <h3 class="section-title">From</h3>
                            <div class="section-content">
                                <p><strong>Your Company Name</strong></p>
                                <p>123 Business Street</p>
                                <p>City, State 10001</p>
                                <p>Email: billing@yourcompany.com</p>
                                <p>Phone: (123) 456-7890</p>
                            </div>
                        </div>

                        <div class="invoice-section">
                            <h3 class="section-title">To</h3>
                            <div class="section-content">
                                <p><strong><?php echo htmlspecialchars($invoice['client_name']); ?></strong></p>
                                <p><?php echo htmlspecialchars($invoice['client_address']); ?></p>
                                <p><?php echo htmlspecialchars($invoice['client_email']); ?></p>
                                <p><?php echo htmlspecialchars($invoice['client_phone']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="invoice-section">
                            <h3 class="section-title">Invoice Info</h3>
                            <div class="section-content">
                                <p><strong>Invoice #:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
                                <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?></p>
                                <p><strong>Due Date:</strong> <?php echo date('M d, Y', strtotime($invoice['due_date'])); ?></p>
                                <p><strong>Payment Terms:</strong> Net 30</p>
                            </div>
                        </div>

                        <div class="invoice-section">
                            <h3 class="section-title">Additional Notes</h3>
                            <div class="section-content">
                                <?php echo nl2br(htmlspecialchars($invoice['notes'] ?: 'No additional notes')); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Description</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $items = json_decode($invoice['items'], true);
                        foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td class="text-right"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td class="text-right">$<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td class="text-right">$<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">$<?php echo number_format($invoice['subtotal'], 2); ?></td>
                    </tr>
                    <tr>
                        <td>Tax (<?php echo htmlspecialchars($invoice['tax_rate']); ?>%):</td>
                        <td class="text-right">$<?php echo number_format($invoice['tax_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td>Discount:</td>
                        <td class="text-right">$<?php echo number_format($invoice['discount_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td class="text-right"><strong>$<?php echo number_format($invoice['total_amount'], 2); ?></strong></td>
                    </tr>
                </table>

                <div class="invoice-actions">
                    <button class="btn btn-outline" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button class="btn btn-primary" onclick="window.location.href='edit_invoice.php?id=<?php echo $invoice['id']; ?>'">
                        <i class="fas fa-edit"></i> Edit Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>