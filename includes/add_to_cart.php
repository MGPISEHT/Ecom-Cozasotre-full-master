<?php
// filepath: c:\xampp\htdocs\Assignment\cozastore-master\includes\add_to_cart.php
include 'db/DBconnnect.php';

session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['name']) || !isset($data['price']) || !isset($data['quantity'])) {
    echo json_encode(['error' => 'Invalid product data']);
    exit;
}

$product = [
    'id' => $data['id'],
    'name' => $data['name'],
    'price' => $data['price'],
    'image' => $data['image'],
    'quantity' => $data['quantity']
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the product is already in the cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] === $product['id']) {
        $item['quantity'] += $product['quantity'];
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = $product;
}

echo json_encode($_SESSION['cart']);