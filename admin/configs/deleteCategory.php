<?php
// Include the database connection file
include("DBconnect.php");

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Prepare the SQL query to delete the category
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // Bind the parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Redirect back to the categories page with a success message
        header("Location: ./components/viewCategories.php?message=Category deleted successfully!");
        exit();
    } catch (PDOException $e) {
        // Handle errors
        echo "Error: " . $e->getMessage();
    }
} else {
}
?>