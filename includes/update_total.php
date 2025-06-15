<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cart'])) {
    $cart = $_POST['cart'];
    $total = 0;
    $item_totals = [];

    foreach ($cart as $item) {
        $id = $item['id'];
        $quantity = intval($item['quantity']);

        foreach ($_SESSION['cart'] as &$sessionItem) {
            if ($sessionItem['id'] == $id) {
                $sessionItem['quantity'] = max(1, $quantity);
            }
        }
    }

    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $item_totals[] = $subtotal;
    }

    echo json_encode(["success" => true, "total" => $total, "item_totals" => $item_totals]);
    exit;
}

echo json_encode(["success" => false]);
exit;
?>
