<?php
// Include the database connection file
include("configs/DBconnect.php");

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Prepare the SQL query to delete the invoice
        $sql = "DELETE FROM invoices WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // Bind the parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Redirect back to the invoices page with a success message
        header("Location: viewInvoices.php?message=Invoice deleted successfully!");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // If 'id' is not set, redirect back to the invoices page
    header("Location: viewInvoices.php");
    exit();
}
?>