<?php
// Start the session (important for messages)
session_start();
include '../configs/DBconnect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productIdToDelete = $_GET['id'];

    try {
        // Prepare the SQL query to delete the product
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // Bind the parameter
        $stmt->bindParam(':id', $productIdToDelete, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Check if any rows were affected (meaning the product existed and was deleted)
        if ($stmt->rowCount() > 0) {
            // Set a success message
            $_SESSION['message'] = "Product deleted successfully!";
            // Redirect to the products page
            header("Location: ../viewProducts.php"); // Adjust path if necessary
            exit();
        } else {
            // Set an error message if the product wasn't found
            $_SESSION['message'] = "Product not found or already deleted.";
            // Redirect with an error message if the product wasn't found
            header("Location: ../viewProducts.php"); // Adjust path if necessary
            exit();
        }
    } catch (PDOException $e) {
        // Set an error message for database issues
        $_SESSION['message'] = "Error deleting product: " . $e->getMessage();
        // Redirect with an error message if there's a database issue
        header("Location: ../viewProducts.php"); // Adjust path if necessary
        exit();
    }
} else {
    // If 'id' is not set or not numeric
    $_SESSION['message'] = "Invalid product ID provided.";
    header("Location: ../viewProducts.php"); // Adjust path if necessary
    exit();
}
?>