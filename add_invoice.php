<?php
require_once 'config.php';
require_once 'functions.php'; // Ensure functions.php is included

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    // --- Existing data gathering and sanitization ---
    $invoice_number = $conn->real_escape_string($_POST['invoice_number'] ?? '');
    $client_name = $conn->real_escape_string($_POST['client_name'] ?? '');
    $client_email = $conn->real_escape_string($_POST['client_email'] ?? '');
    $client_phone = $conn->real_escape_string($_POST['client_phone'] ?? '');
    $client_address = $conn->real_escape_string($_POST['client_address'] ?? '');
    $invoice_date = $conn->real_escape_string($_POST['invoice_date'] ?? '');
    $due_date = $conn->real_escape_string($_POST['due_date'] ?? '');
    $status = $conn->real_escape_string($_POST['status'] ?? '');
    $notes = $conn->real_escape_string($_POST['notes'] ?? '');
    
    // Process items for current invoice structure and MyInvois lineItems
    $items = []; // For current database structure
    $myinvois_lineItems = []; // For MyInvois validation structure
    $subtotal = 0;

    if (isset($_POST['item_name']) && is_array($_POST['item_name'])) {
        foreach ($_POST['item_name'] as $index => $name) {
            if (!empty($name)) {
                $quantity = floatval($_POST['item_quantity'][$index] ?? 0);
                $unit_price = floatval($_POST['item_unit_price'][$index] ?? 0);
                $line_total = $quantity * $unit_price; // Calculate line total for MyInvois

                $items[] = [
                    'name' => $conn->real_escape_string($name),
                    'description' => $conn->real_escape_string($_POST['item_description'][$index] ?? ''),
                    'quantity' => $quantity,
                    'unit_price' => $unit_price
                ];

                $myinvois_lineItems[] = [
                    'description' => $conn->real_escape_string($_POST['item_description'][$index] ?? ''),
                    'quantity' => $quantity,
                    'unitPrice' => $unit_price,
                    'totalAmount' => $line_total // totalAmount for this specific line item
                ];

                $subtotal += $line_total;
            }
        }
    }
    
    $tax_rate = floatval($_POST['tax_rate'] ?? 0);
    $tax_amount = $subtotal * ($tax_rate / 100);
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $total_amount = $subtotal + $tax_amount - $discount_amount;
    // --- End existing data gathering ---

    // --- Prepare data for MyInvois validation ---
    // Note: Some fields like sellerId, buyerId, invoiceType, currencyCode are not in the current form.
    // You'll need to define how these are obtained (e.g., from a config file, user session, or added to the form).
    // For demonstration, placeholders are used.
    $invoiceForMyinvois = [
        'invoiceNumber' => $invoice_number,
        'invoiceDate' => $invoice_date,
        'sellerId' => '123456789012', // Placeholder: Replace with actual seller TIN from config/database
        'buyerId' => '987654321098',   // Placeholder: Replace with actual buyer TIN/NRIC
        'buyerName' => $client_name,
        'invoiceType' => '01',         // Placeholder: Example type, adjust as needed
        'totalAmount' => $total_amount, // Overall invoice total
        'currencyCode' => 'MYR',       // Placeholder: Hardcoded for MyInvois
        'taxAmount' => $tax_amount,     // Overall tax amount
        'invoiceStatus' => $status,
        'lineItems' => $myinvois_lineItems
    ];

    // --- Perform MyInvois validation ---
    $validationErrors = validateInvoiceForMyinvois($invoiceForMyinvois);

    if (!empty($validationErrors)) {
        // Validation failed, display errors to the user
        $errorMessage = "<ul>";
        foreach ($validationErrors as $error) {
            $errorMessage .= "<li>" . htmlspecialchars($error) . "</li>";
        }
        $errorMessage .= "</ul>";
        // You would typically echo this message on the page or redirect with a flash message
        echo "<div style='color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px;'>";
        echo "<h3>MyInvois Validation Errors:</h3>";
        echo $errorMessage;
        echo "</div>";
        // Stop further processing
        exit; 
    } else {
        // Validation passed, proceed with database insertion

        // Convert items array to JSON for storage
        $items_json = json_encode($items);
        
        // Insert new invoice
        $stmt = $conn->prepare("INSERT INTO invoices (
            invoice_number, 
            client_name, 
            client_email, 
            client_phone, 
            client_address, 
            invoice_date, 
            due_date, 
            status, 
            notes, 
            items, 
            subtotal, 
            tax_rate, 
            tax_amount, 
            discount_amount, 
            total_amount
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssssddddd", 
            $invoice_number, 
            $client_name, 
            $client_email, 
            $client_phone, 
            $client_address, 
            $invoice_date, 
            $due_date, 
            $status, 
            $notes, 
            $items_json, // Use the JSON string here
            $subtotal, 
            $tax_rate, 
            $tax_amount, 
            $discount_amount, 
            $total_amount
        );
        
        if ($stmt->execute()) {
            echo "New invoice added successfully!";
            // Redirect to a success page or invoice list
            // header('Location: index.php'); 
            // exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Invoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Basic styling for the form */
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 20px auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        h2 { color: #333; text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1em;
            margin-top: 5px;
            box-sizing: border-box;
        }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .item-row { display: flex; gap: 10px; align-items: flex-end; margin-bottom: 10px; border: 1px dashed #e0e0e0; padding: 10px; border-radius: 5px; background-color: #fdfdfd; }
        .item-row .form-group { margin-bottom: 0; flex: 1; }
        .add-item-btn, .remove-item-btn {
            background-color: var(--primary);
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            margin-top: 10px;
        }
        .remove-item-btn { background-color: var(--danger); }
        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: var(--success);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 20px;
        }
        button[type="submit"]:hover { opacity: 0.9; }
        .total-section {
            border-top: 1px solid #eee;
            margin-top: 20px;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            font-size: 1.1em;
        }
        .total-line .label { font-weight: 500; }
        .total-line .value { font-weight: 600; }
        .total-line.grand-total { font-size: 1.3em; font-weight: 700; color: var(--primary); }

        /* Styling for alert messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Invoice</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
        <?php elseif (isset($successMessage)): ?>
            <div class="alert alert-success">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="invoice_number">Invoice Number:</label>
                    <input type="text" id="invoice_number" name="invoice_number" required>
                </div>
                <div class="form-group">
                    <label for="invoice_date">Invoice Date:</label>
                    <input type="date" id="invoice_date" name="invoice_date" required>
                </div>
                <div class="form-group">
                    <label for="due_date">Due Date:</label>
                    <input type="date" id="due_date" name="due_date" required>
                </div>
            </div>

            <div class="form-group">
                <label for="client_name">Client Name:</label>
                <input type="text" id="client_name" name="client_name" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="client_email">Client Email:</label>
                    <input type="email" id="client_email" name="client_email">
                </div>
                <div class="form-group">
                    <label for="client_phone">Client Phone:</label>
                    <input type="text" id="client_phone" name="client_phone">
                </div>
            </div>
            <div class="form-group">
                <label for="client_address">Client Address:</label>
                <textarea id="client_address" name="client_address" rows="3"></textarea>
            </div>

            <h3>Invoice Items</h3>
            <div id="invoice-items">
                <div class="item-row">
                    <div class="form-group" style="flex: 2;">
                        <label>Item Name:</label>
                        <input type="text" name="item_name[]" placeholder="Item Name">
                    </div>
                    <div class="form-group" style="flex: 3;">
                        <label>Description:</label>
                        <input type="text" name="item_description[]" placeholder="Description">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Quantity:</label>
                        <input type="number" name="item_quantity[]" step="any" min="0" value="0">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Unit Price:</label>
                        <input type="number" name="item_unit_price[]" step="0.01" min="0" value="0.00">
                    </div>
                    <div class="form-group">
                        <button type="button" class="remove-item-btn" style="background-color: #dc3545;">Remove</button>
                    </div>
                </div>
            </div>
            <button type="button" class="add-item-btn">Add Item</button>

            <div class="total-section">
                <div class="form-row">
                    <div class="form-group">
                        <label for="tax_rate">Tax Rate (%):</label>
                        <input type="number" id="tax_rate" name="tax_rate" step="0.01" min="0" value="0.00">
                    </div>
                    <div class="form-group">
                        <label for="discount_amount">Discount Amount:</label>
                        <input type="number" id="discount_amount" name="discount_amount" step="0.01" min="0" value="0.00">
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="Draft">Draft</option>
                            <option value="Pending">Pending</option>
                            <option value="Paid">Paid</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>

            <button type="submit">Add Invoice</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const invoiceItems = document.getElementById('invoice-items');
            const addItemBtn = document.querySelector('.add-item-btn');

            addItemBtn.addEventListener('click', function() {
                const newItemRow = document.createElement('div');
                newItemRow.classList.add('item-row');
                newItemRow.innerHTML = `
                    <div class="form-group" style="flex: 2;">
                        <label>Item Name:</label>
                        <input type="text" name="item_name[]" placeholder="Item Name">
                    </div>
                    <div class="form-group" style="flex: 3;">
                        <label>Description:</label>
                        <input type="text" name="item_description[]" placeholder="Description">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Quantity:</label>
                        <input type="number" name="item_quantity[]" step="any" min="0" value="0">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Unit Price:</label>
                        <input type="number" name="item_unit_price[]" step="0.01" min="0" value="0.00">
                    </div>
                    <div class="form-group">
                        <button type="button" class="remove-item-btn" style="background-color: #dc3545;">Remove</button>
                    </div>
                `;
                invoiceItems.appendChild(newItemRow);
            });

            invoiceItems.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-item-btn')) {
                    if (invoiceItems.children.length > 1) { // Ensure at least one row remains
                        event.target.closest('.item-row').remove();
                    } else {
                        alert("You must have at least one invoice item.");
                    }
                }
            });
        });
    </script>
</body>
</html>