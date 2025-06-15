<?php
// Include the database connection file
include("configs/DBconnect.php");

// Fetch the invoice to edit
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "SELECT * FROM invoices WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            header("Location: viewInvoices.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: viewInvoices.php");
    exit();
}

// Handle form submission
if (isset($_POST['update-invoice'])) {
    $id = $_POST['id'];
    $invoiceNumber = $_POST['invoice_number'];
    $customerName = $_POST['customer_name'];
    $invoiceDate = $_POST['invoice_date'];
    $totalAmount = $_POST['total_amount'];
    $status = $_POST['status'];

    try {
        $sql = "UPDATE invoices 
                SET invoice_number = :invoice_number, customer_name = :customer_name, 
                    invoice_date = :invoice_date, total_amount = :total_amount, status = :status 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':invoice_number', $invoiceNumber);
        $stmt->bindParam(':customer_name', $customerName);
        $stmt->bindParam(':invoice_date', $invoiceDate);
        $stmt->bindParam(':total_amount', $totalAmount);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Redirect to the invoices page with a success message
        header("Location: viewInvoices.php?message=Invoice updated successfully!");
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
    <title>Edit Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Invoice</h2>
        <form action="editInvoice.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($invoice['id']); ?>">

            <!-- Invoice Number -->
            <div class="mb-3">
                <label for="invoice_number" class="form-label">Invoice Number</label>
                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" required>
            </div>

            <!-- Customer Name -->
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($invoice['customer_name']); ?>" required>
            </div>

            <!-- Invoice Date -->
            <div class="mb-3">
                <label for="invoice_date" class="form-label">Invoice Date</label>
                <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>" required>
            </div>

            <!-- Total Amount -->
            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" value="<?php echo htmlspecialchars($invoice['total_amount']); ?>" required>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Paid" <?php echo $invoice['status'] === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="Unpaid" <?php echo $invoice['status'] === 'Unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="update-invoice" class="btn btn-primary">Update Invoice</button>
        </form>
    </div>
</body>
</html>