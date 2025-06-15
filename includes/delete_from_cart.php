<?php
    header('Content-Type: application/json');

    // Start the session (if you are using sessions)
    session_start();

    // Get the JSON data sent from JavaScript
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if (isset($data['id'])) {
        $item_id_to_delete = $data['id'];

        // Assuming your cart data is stored in a session variable called 'cart'
        if (isset($_SESSION['cart'])) {
            // Filter out the item to be deleted
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($item_id_to_delete) {
                return $item['id'] != $item_id_to_delete;
            });

            // Return the updated cart data as JSON
            echo json_encode(array_values($_SESSION['cart'])); // Use array_values to re-index the array
            exit();
        } else {
            // Cart is empty
            echo json_encode([]);
            exit();
        }
    } else {
        // No ID provided
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Item ID not provided']);
        exit();
    }
?>