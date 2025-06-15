<?php
include './function.php'; // Include your database connection

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Return the product details as JSON
        header('Content-Type: application/json');
        echo json_encode($product);
    } else {
        // Product not found
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    // Invalid product ID
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
}
?>