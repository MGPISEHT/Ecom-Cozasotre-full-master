<?php
// Include the database connection file
include("configs/DBconnect.php");

// Handle form submission
if (isset($_POST['add-invoice'])) {
    $invoiceNumber = $_POST['invoice_number'];
    $customerName = $_POST['customer_name'];
    $invoiceDate = $_POST['invoice_date'];
    $totalAmount = $_POST['total_amount'];
    $status = $_POST['status'];

    try {
        // Prepare the SQL query
        $sql = "INSERT INTO invoices (invoice_number, customer_name, invoice_date, total_amount, status) 
                VALUES (:invoice_number, :customer_name, :invoice_date, :total_amount, :status)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':invoice_number', $invoiceNumber);
        $stmt->bindParam(':customer_name', $customerName);
        $stmt->bindParam(':invoice_date', $invoiceDate);
        $stmt->bindParam(':total_amount', $totalAmount);
        $stmt->bindParam(':status', $status);

        // Execute the query
        $stmt->execute();

        // Redirect to the invoices page with a success message
        header("Location: viewInvoices.php?message=Invoice added successfully!");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Invoice</h2>
        <form action="addInvoice.php" method="post">
            <!-- Invoice Number -->
            <div class="mb-3">
                <label for="invoice_number" class="form-label">Invoice Number</label>
                <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
            </div>

            <!-- Customer Name -->
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>

            <!-- Invoice Date -->
            <div class="mb-3">
                <label for="invoice_date" class="form-label">Invoice Date</label>
                <input type="date" class="form-control" id="invoice_date" name="invoice_date" required>
            </div>

            <!-- Total Amount -->
            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" required>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Paid">Paid</option>
                    <option value="Unpaid">Unpaid</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="add-invoice" class="btn btn-primary">Add Invoice</button>
        </form>
    </div>
</body>
</html>