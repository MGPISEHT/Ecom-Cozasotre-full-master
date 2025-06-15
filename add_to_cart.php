<?php
session_start();
header('Content-Type: application/json');

// Read POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productId = $data['id'];
$found = false;

// Update quantity if the product exists
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] === $productId) {
        $item['quantity'] += $data['quantity'];
        $found = true;
        break;
    }
}

// Add new product if not found
if (!$found) {
    $_SESSION['cart'][] = [
        'id' => $data['id'],
        'name' => $data['name'],
        'price' => $data['price'],
        'image' => $data['image'],
        'quantity' => $data['quantity']
    ];
}

// Return updated cart as JSON
echo json_encode($_SESSION['cart']);
?>
