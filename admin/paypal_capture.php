<?php
session_start();
include './db/DBconnnect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

try {
    $customer_id = $data['customer_id'] ?? null;
    $recipient_name = $data['recipient_name'] ?? null;
    $email = $data['email'] ?? null;
    $shipping_phone = $data['shipping_phone'] ?? null;
    $country = $data['country'] ?? null;
    $address_line1 = $data['address_line1'] ?? null;
    $total_amount = $data['total_amount'] ?? 0;
    $paymentID = $data['paymentID'] ?? null;

    // Insert into orders table
    $stmtOrder = $conn->prepare("INSERT INTO orders (customer_id, status, created_at, updated_at, order_cost, customer_country, customer_phone, customer_address, order_date) VALUES (?, ?, NOW(), NOW(), ?, ?, ?, ?, NOW())");
    $stmtOrder->execute([$customer_id, 'paid', $total_amount, $country, $shipping_phone, $address_line1]);
    $new_order_id = $conn->lastInsertId();

    // Insert into payments table
    $stmtPayment = $conn->prepare("INSERT INTO payments (order_id, payment_method, amount, payment_status, transaction_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmtPayment->execute([$new_order_id, 'PayPal', $total_amount, 'completed', $paymentID]);

    echo json_encode(['success' => true, 'order_id' => $new_order_id]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
