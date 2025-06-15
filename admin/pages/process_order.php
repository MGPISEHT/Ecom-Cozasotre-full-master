<?php
session_start();
include './configs/DBconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_order'])) {
    try {
        $conn->beginTransaction();

        $customer_id = $_POST['customer_id'];
        $product_ids = $_POST['product_id'];
        $quantities = $_POST['quantity'];

        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (customer_id) VALUES (?)");
        $stmt->execute([$customer_id]);
        $order_id = $conn->lastInsertId();

        $total_amount = 0;
        foreach ($product_ids as $index => $product_id) {
            $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            $price = $product['price'];

            $quantity = $quantities[$index];
            $subtotal = $price * $quantity;
            $total_amount += $subtotal;

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $product_id, $quantity, $subtotal]);
        }

        // Update total amount
        $stmt = $conn->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");
        $stmt->execute([$total_amount, $order_id]);

        $conn->commit();
        $_SESSION['message'] = "Order created successfully!";
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

header("Location: create_order.php");
exit;
?>
    