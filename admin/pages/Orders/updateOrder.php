<?php
session_start();
require '../configs/DBconnect.php'; 

if (isset($_POST['update-order'])) { 
    $item_id = $_POST['item_id'];
    $order_id = $_POST['order_id'];
    $product_name = $_POST['product_name']; 
    $quantity = $_POST['quantity'];      
    $price = $_POST['price'];            

    // Basic validation
    if (empty($item_id) || !is_numeric($item_id)) {
        $_SESSION['message'] = "Invalid Order Item ID provided.";
        header("Location: ../../viewOrder.php"); 
        exit();
    }

    try {
        $sql = "UPDATE order_items SET 
                    order_id = :order_id,
                    product_name = :product_name, 
                    quantity = :quantity, 
                    price = :price 
                WHERE item_id = :item_id";
        
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT); 
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price); 
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Order item updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update order item.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database Error: " . $e->getMessage();
    }

    header("Location: ../../viewOrder.php"); 
    exit();
}

?>