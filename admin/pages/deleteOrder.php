<?php
session_start();
include '../configs/DBconnect.php'; 
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $orderItemIdToDelete = intval($_GET['id']); 

    try {
        $sql = "DELETE FROM order_items WHERE item_id = :item_id";
        $stmt = $conn->prepare($sql);

        // Bind the parameter to prevent SQL injection
        $stmt->bindParam(':item_id', $orderItemIdToDelete, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Order item deleted successfully!";
        } else {
            $_SESSION['message'] = "Order item not found or already deleted.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database Error: Failed to delete order item. " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "Invalid order item ID provided for deletion.";
}
header("Location: ../viewOrder.php");
?>