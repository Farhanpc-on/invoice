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

if (!$invoice) {
    header("Location: records.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $invoice_number = $conn->real_escape_string($_POST['invoice_number']);
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $client_email = $conn->real_escape_string($_POST['client_email']);
    $client_phone = $conn->real_escape_string($_POST['client_phone']);
    $client_address = $conn->real_escape_string($_POST['client_address']);
    $invoice_date = $conn->real_escape_string($_POST['invoice_date']);
    $due_date = $conn->real_escape_string($_POST['due_date']);
    $status = $conn->real_escape_string($_POST['status']);
    $notes = $conn->real_escape_string($_POST['notes']);
    
    // Process items
    $items = [];
    foreach ($_POST['item_name'] as $index => $name) {
        if (!empty($name)) {
            $items[] = [
                'name' => $conn->real_escape_string($name),
                'description' => $conn->real_escape_string($_POST['item_description'][$index]),
                'quantity' => floatval($_POST['item_quantity'][$index]),
                'unit_price' => floatval($_POST['item_price'][$index])
            ];
        }
    }
    
    $items_json = json_encode($items);
    
    // Calculate totals
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['quantity'] * $item['unit_price'];
    }
    
    $tax_rate = floatval($_POST['tax_rate']);
    $tax_amount = $subtotal * ($tax_rate / 100);
    $discount_amount = floatval($_POST['discount_amount']);
    $total_amount = $subtotal + $tax_amount - $discount_amount;
    
    // Update invoice in database
    $stmt = $conn->prepare("UPDATE invoices SET 
        invoice_number = ?, 
        client_name = ?, 
        client_email = ?, 
        client_phone = ?, 
        client_address = ?, 
        invoice_date = ?, 
        due_date = ?, 
        status = ?, 
        notes = ?, 
        items = ?, 
        subtotal = ?, 
        tax_rate = ?, 
        tax_amount = ?, 
        discount_amount = ?, 
        total_amount = ? 
        WHERE id = ?");
    
    $stmt->bind_param("ssssssssssdddddi", 
        $invoice_number, 
        $client_name, 
        $client_email, 
        $client_phone, 
        $client_address, 
        $invoice_date, 
        $due_date, 
        $status, 
        $notes, 
        $items_json, 
        $subtotal, 
        $tax_rate, 
        $tax_amount, 
        $discount_amount, 
        $total_amount, 
        $id);
    
    if ($stmt->execute()) {
        header("Location: view_invoice.php?id=$id");
        exit();
    } else {
        $error = "Error updating invoice: " . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invoice - Invoice Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-top: 20px;
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--gray);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--gray-light);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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
        
        .add-item-btn {
            background-color: var(--primary-light);
            color: var(--primary);
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .add-item-btn:hover {
            background-color: rgba(67, 97, 238, 0.2);
        }
        
        .remove-item-btn {
            background-color: transparent;
            border: none;
            color: var(--danger);
            cursor: pointer;
        }
        
        .totals-section {
            margin-top: 20px;
            padding: 20px;
            background-color: var(--primary-light);
            border-radius: 8px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .totals-label {
            font-weight: 500;
        }
        
        .totals-value {
            font-weight: 600;
        }
        
        .form-actions {
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
        
        .error-message {
            color: var(--danger);
            margin-top: 5px;
            font-size: 0.9rem;
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
                <h1>Edit Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4361ee&color=fff" alt="User">
                    <span>Admin</span>
                </div>
            </header>

            <!-- Form Container -->
            <div class="form-container">
                <?php if (isset($error)): ?>
                    <div style="color: var(--danger); margin-bottom: 20px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="edit_invoice.php?id=<?php echo $id; ?>" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="invoice_number">Invoice Number</label>
                            <input type="text" class="form-control" id="invoice_number" name="invoice_number" 
                                   value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" <?php echo $invoice['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="paid" <?php echo $invoice['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="overdue" <?php echo $invoice['status'] === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="invoice_date">Invoice Date</label>
                            <input type="date" class="form-control" id="invoice_date" name="invoice_date" 
                                   value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="due_date">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" 
                                   value="<?php echo htmlspecialchars($invoice['due_date']); ?>" required>
                        </div>
                    </div>

                    <h3 class="form-title">Client Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="client_name">Client Name</label>
                            <input type="text" class="form-control" id="client_name" name="client_name" 
                                   value="<?php echo htmlspecialchars($invoice['client_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="client_email">Client Email</label>
                            <input type="email" class="form-control" id="client_email" name="client_email" 
                                   value="<?php echo htmlspecialchars($invoice['client_email']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="client_phone">Client Phone</label>
                            <input type="tel" class="form-control" id="client_phone" name="client_phone" 
                                   value="<?php echo htmlspecialchars($invoice['client_phone']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="client_address">Client Address</label>
                            <textarea class="form-control" id="client_address" name="client_address" rows="2"><?php echo htmlspecialchars($invoice['client_address']); ?></textarea>
                        </div>
                    </div>

                    <h3 class="form-title">Items</h3>
                    <table class="items-table" id="items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $items = json_decode($invoice['items'], true);
                            foreach ($items as $index => $item): ?>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="item_name[]" 
                                               value="<?php echo htmlspecialchars($item['name']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="item_description[]" 
                                               value="<?php echo htmlspecialchars($item['description']); ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="item_quantity[]" min="0" step="0.01" 
                                               value="<?php echo htmlspecialchars($item['quantity']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="item_price[]" min="0" step="0.01" 
                                               value="<?php echo htmlspecialchars($item['unit_price']); ?>" required>
                                    </td>
                                    <td>
                                        $<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?>
                                    </td>
                                    <td>
                                        <button type="button" class="remove-item-btn" onclick="removeItem(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="button" class="add-item-btn" onclick="addItem()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>

                    <div class="totals-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="tax_rate">Tax Rate (%)</label>
                                <input type="number" class="form-control" id="tax_rate" name="tax_rate" min="0" max="100" step="0.01" 
                                       value="<?php echo htmlspecialchars($invoice['tax_rate']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="discount_amount">Discount Amount</label>
                                <input type="number" class="form-control" id="discount_amount" name="discount_amount" min="0" step="0.01" 
                                       value="<?php echo htmlspecialchars($invoice['discount_amount']); ?>">
                            </div>
                        </div>
                        
                        <div class="totals-row">
                            <span class="totals-label">Subtotal:</span>
                            <span class="totals-value" id="subtotal">$<?php echo number_format($invoice['subtotal'], 2); ?></span>
                        </div>
                        <div class="totals-row">
                            <span class="totals-label">Tax:</span>
                            <span class="totals-value" id="tax-amount">$<?php echo number_format($invoice['tax_amount'], 2); ?></span>
                        </div>
                        <div class="totals-row">
                            <span class="totals-label">Discount:</span>
                            <span class="totals-value" id="discount-amount">$<?php echo number_format($invoice['discount_amount'], 2); ?></span>
                        </div>
                        <div class="totals-row">
                            <span class="totals-label"><strong>Total:</strong></span>
                            <span class="totals-value"><strong id="total-amount">$<?php echo number_format($invoice['total_amount'], 2); ?></strong></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($invoice['notes']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="window.history.back()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add new item row
        function addItem() {
            const table = document.getElementById('items-table').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            
            newRow.innerHTML = `
                <td>
                    <input type="text" class="form-control" name="item_name[]" required>
                </td>
                <td>
                    <input type="text" class="form-control" name="item_description[]">
                </td>
                <td>
                    <input type="number" class="form-control" name="item_quantity[]" min="0" step="0.01" value="1" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="item_price[]" min="0" step="0.01" value="0" required>
                </td>
                <td>
                    $0.00
                </td>
                <td>
                    <button type="button" class="remove-item-btn" onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            // Add event listeners to new inputs
            const inputs = newRow.getElementsByTagName('input');
            for (let input of inputs) {
                input.addEventListener('input', calculateTotals);
            }
        }
        
        // Remove item row
        function removeItem(button) {
            const row = button.closest('tr');
            row.remove();
            calculateTotals();
        }
        
        // Calculate totals
        function calculateTotals() {
            let subtotal = 0;
            const rows = document.getElementById('items-table').getElementsByTagName('tbody')[0].rows;
            
            for (let row of rows) {
                const inputs = row.getElementsByTagName('input');
                const quantity = parseFloat(inputs[2].value) || 0;
                const price = parseFloat(inputs[3].value) || 0;
                const amount = quantity * price;
                
                // Update amount cell
                row.cells[4].textContent = '$' + amount.toFixed(2);
                subtotal += amount;
            }
            
            // Update subtotal
            document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
            
            // Calculate tax
            const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
            const taxAmount = subtotal * (taxRate / 100);
            document.getElementById('tax-amount').textContent = '$' + taxAmount.toFixed(2);
            
            // Get discount
            const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
            document.getElementById('discount-amount').textContent = '$' + discountAmount.toFixed(2);
            
            // Calculate total
            const total = subtotal + taxAmount - discountAmount;
            document.getElementById('total-amount').textContent = '$' + total.toFixed(2);
        }
        
        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Add listeners to existing inputs
            const inputs = document.querySelectorAll('input[name="item_quantity[]"], input[name="item_price[]"]');
            for (let input of inputs) {
                input.addEventListener('input', calculateTotals);
            }
            
            // Add listeners to tax and discount inputs
            document.getElementById('tax_rate').addEventListener('input', calculateTotals);
            document.getElementById('discount_amount').addEventListener('input', calculateTotals);
        });
    </script>
</body>
</html>