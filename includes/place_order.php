<?php
// Start session at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db/DBconnnect.php';

// Check if DB connection is established
if (!isset($conn)) {
    die("ERROR: Database connection not established. Please check 'db/DBconnnect.php'.");
}

// --- Utility Functions ---
function getCart(): array
{
    return $_SESSION['cart'] ?? [];
}

function updateCart(array $newCart): void
{
    $_SESSION['cart'] = $newCart;
}

function calculateCartTotal(array $cart): float
{
    $total = 0.0;
    foreach ($cart as $item) {
        $price = filter_var($item['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($item['quantity'] ?? 0, FILTER_VALIDATE_INT);
        if ($price !== false && $quantity !== false && $quantity >= 0) {
            $total += ($price * $quantity);
        }
    }
    return $total;
}

// **Revised getPayerInfo() to ensure all expected keys exist**
function getPayerInfo(): array
{
    $defaultPayerInfo = [
        'customer_id' => $_SESSION['customer_id'] ?? null, // Try to get from session if available
        'username' => '',
        'email' => '',
        'phone' => '',
        'country' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
    ];

    // Merge session data with defaults to ensure all keys exist for pre-filling the form
    return array_merge($defaultPayerInfo, $_SESSION['payer_info'] ?? []);
}


// --- AJAX Handling for Cart Operations (addToCart, removeItem, updateQuantities) ---
if (isset($_POST['action'])) {
    $response = ['success' => false, 'message' => 'An unknown error occurred.'];
    header('Content-Type: application/json');

    if ($_POST['action'] === 'addToCart') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_URL);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if ($id !== false && $name && $price !== false && $image && $quantity !== false && $quantity > 0) {
            $itemToAdd = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'quantity' => $quantity
            ];
            $cart = getCart();
            $itemFound = false;
            foreach ($cart as &$item) {
                if ($item['id'] === $itemToAdd['id']) {
                    $item['quantity'] += $itemToAdd['quantity'];
                    $itemFound = true;
                    break;
                }
            }
            if (!$itemFound) {
                $cart[] = $itemToAdd;
            }
            updateCart($cart);
            $response = ['success' => true, 'message' => 'Item added to cart.', 'cartItemCount' => count($cart)];
        } else {
            $response['message'] = 'Invalid item data provided for adding to cart.';
        }
    } elseif ($_POST['action'] === 'removeItem') {
        $itemId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($itemId !== false) {
            $cart = getCart();
            $itemRemoved = false;
            foreach ($cart as $key => $item) {
                if ($item['id'] === $itemId) {
                    unset($cart[$key]);
                    $itemRemoved = true;
                    break;
                }
            }
            updateCart(array_values($cart));
            if ($itemRemoved) {
                $response = ['success' => true, 'message' => 'Item removed successfully.', 'cartItemCount' => count(getCart())];
            } else {
                $response['message'] = 'Item not found in cart.';
            }
        } else {
            $response['message'] = 'Invalid item ID for removal.';
        }
    } elseif ($_POST['action'] === 'updateQuantities') {
        $cartData = $_POST['cart'] ?? [];

        if (is_array($cartData)) {
            $cart = getCart();
            $updatedAny = false;
            foreach ($cartData as $itemData) {
                $itemId = filter_var($itemData['id'] ?? 0, FILTER_VALIDATE_INT);
                $newQuantity = filter_var($itemData['quantity'] ?? 0, FILTER_VALIDATE_INT);

                if ($itemId !== false && $newQuantity !== false && $newQuantity >= 0) {
                    foreach ($cart as &$item) {
                        if ($item['id'] === $itemId) {
                            if ($item['quantity'] !== $newQuantity) {
                                $item['quantity'] = $newQuantity;
                                $updatedAny = true;
                            }
                            break;
                        }
                    }
                } else {
                    error_log("Invalid item data received for quantity update: " . print_r($itemData, true));
                }
            }
            updateCart($cart);
            $newCart = getCart();
            $total = calculateCartTotal($newCart);
            $itemTotals = [];
            foreach ($newCart as $item) {
                $itemTotals[] = number_format($item['price'] * $item['quantity'], 2);
            }

            $response = [
                'success' => $updatedAny,
                'total' => number_format($total, 2),
                'item_totals' => $itemTotals,
                'message' => $updatedAny ? 'Cart quantities updated.' : 'No quantities were updated.',
                'cartItemCount' => count($newCart)
            ];
        } else {
            $response['message'] = 'Invalid data for quantity update. Expected an array.';
        }
    }
    echo json_encode($response);
    exit(); // IMPORTANT: Exit after sending JSON response for AJAX requests
}


// --- Order Placement Handling (for both PayPal and Cash on Delivery) ---
if (isset($_POST['place_order'])) {
    // 1. Sanitize and Validate Input from the Form
    $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
    // Fallback to session customer_id if not explicitly selected/sent from form
    if ($customer_id === false || $customer_id === null) {
        $customer_id = $_SESSION['customer_id'] ?? null;
    }

    // Capture all relevant shipping and contact details from the form
    $recipient_name = filter_input(INPUT_POST, 'recipient_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // Customer email (for contact)
    $shipping_phone = filter_input(INPUT_POST, 'shipping_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Phone for shipping contact
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address_line1 = filter_input(INPUT_POST, 'address_line1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address_line2 = filter_input(INPUT_POST, 'address_line2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $postal_code = filter_input(INPUT_POST, 'postal_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $paypal_transaction_id = filter_input(INPUT_POST, 'paypal_transaction_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Basic validation: Check essential shipping and customer data
    if (!$customer_id || !$recipient_name || !$email || !$shipping_phone || !$country || !$address_line1 || !$city || !$state) {
        $_SESSION['order_error_message'] = "ព័ត៌មានចាំបាច់សម្រាប់ការបញ្ជាទិញមិនពេញលេញ។ សូមបំពេញទម្រង់ឱ្យបានត្រឹមត្រូវ។"; // Required order information is incomplete. Please fill the form correctly.
        header('location: shoping-cart.php'); // Redirect back to cart/checkout page
        exit;
    }

    $cart = getCart();
    if (empty($cart)) {
        $_SESSION['order_error_message'] = "កន្ត្រករបស់អ្នកទទេ។ សូមបន្ថែមទំនិញមុនពេលបញ្ជាទិញ។"; // Your cart is empty. Please add items before placing an order.
        header('location: shoping-cart.php');
        exit;
    }

    $order_cost = calculateCartTotal($cart); // This variable is correctly calculated
    $order_status = ($payment_method === 'PayPal') ? "processing" : "on_hold"; // Set initial status based on payment method

    try {
        $conn->beginTransaction();

        // 2. Insert into `shipping_address` table
        $stmt_shipping = $conn->prepare("INSERT INTO shipping_address (customer_id, recipient_name, address_line1, address_line2, city, state, postal_code, country, phone, created_at, updated_at)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt_shipping->execute([
            $customer_id,
            $recipient_name,
            $address_line1,
            $address_line2, // This could be empty, it's fine
            $city,
            $state,
            $postal_code,   // This could be empty, it's fine
            $country,
            $shipping_phone
        ]);
        $shipping_address_id = $conn->lastInsertId();

        // 3. Insert into `orders` table
        // *** FIX HERE: Changed 'order_cost' to 'total_amount' to match your schema ***
        // *** ASSUMPTION: You have added 'shipping_address_id' column to your 'orders' table ***
        $stmt_orders = $conn->prepare("INSERT INTO orders (customer_id, shipping_address_id, total_amount, status, created_at, updated_at)
                                         VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt_orders->execute([$customer_id, $shipping_address_id, $order_cost, $order_status]);
        $order_id = $conn->lastInsertId();

        // 4. Insert into `order_items` table
        foreach ($cart as $product) {
            $product_id = filter_var($product['id'] ?? 0, FILTER_VALIDATE_INT);
            // Fetch actual product price and name from DB to prevent client-side tampering (RECOMMENDED for production)
            $stmt_product_lookup = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
            $stmt_product_lookup->execute([$product_id]);
            $db_product_data = $stmt_product_lookup->fetch(PDO::FETCH_ASSOC);

            if (!$db_product_data) {
                error_log("Product ID {$product_id} not found in database during order placement for Order ID: " . $order_id);
                throw new Exception("ផលិតផលមួយចំនួននៅក្នុងកន្ត្រកមិនត្រឹមត្រូវទេ។"); // Some products in cart are invalid.
            }

            $actual_product_name = $db_product_data['name'];
            $actual_product_price = $db_product_data['price'];
            $product_quantity = filter_var($product['quantity'] ?? 0, FILTER_VALIDATE_INT);

            if ($product_id !== false && $product_id > 0 && $actual_product_price !== false && $product_quantity !== false && $product_quantity > 0) {
                // Ensure the 'price' column in order_items is being used, not product_price from products table directly
                $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, created_at)
                                                 VALUES (?, ?, ?, ?, NOW())");
                $stmt_items->execute([$order_id, $product_id, $product_quantity, $actual_product_price]);
            } else {
                error_log("Invalid product data (quantity or price) for product ID {$product_id} in cart for Order ID: " . $order_id);
                throw new Exception("ទិន្នន័យផលិតផលមិនត្រឹមត្រូវនៅក្នុងកន្ត្រក។");
            }
        }

        // 5. Insert into `payments` table
        $payment_status = ($payment_method === 'PayPal' && !empty($paypal_transaction_id)) ? 'completed' : 'pending';
        // For production, VERIFY PayPal transaction_id with PayPal API before setting to 'completed'

        $stmt_payment = $conn->prepare("INSERT INTO payments (order_id, payment_method, amount, payment_status, transaction_id, created_at, updated_at)
                                         VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt_payment->execute([$order_id, $payment_method, $order_cost, $payment_status, $paypal_transaction_id]);

        $conn->commit(); // Commit all transactions if all inserts are successful

        unset($_SESSION['cart']); // Clear the cart after successful order placement
        $_SESSION['order_success_message'] = "ការបញ្ជាទិញបានដាក់ដោយជោគជ័យ! លេខសម្គាល់ការបញ្ជាទិញរបស់អ្នកគឺ: " . $order_id;
        header('location: order_success.php?order_id=' . $order_id);
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("DB Error placing order: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        $_SESSION['order_error_message'] = "មានកំហុសមូលដ្ឋានទិន្នន័យបានកើតឡើង។ សូមព្យាយាមម្តងទៀត។ Error: " . $e->getMessage();
        header('location: order_failed.php');
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("General Error placing order: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        $_SESSION['order_error_message'] = "មានកំហុសដែលមិនបានរំពឹងទុកបានកើតឡើង។ សូមព្យាយាមម្តងទៀត។ Error: " . $e->getMessage();
        header('location: order_failed.php');
        exit;
    }
}

// --- Prepare data for HTML display if no POST action occurred ---
// This part will run when the page is first loaded or after a non-order related POST
$cart = getCart();
$total = calculateCartTotal($cart);
$_SESSION['total_cart_cost'] = $total; // Keep total in session for PayPal on client-side

// Re-fetch payer info for displaying in the form fields.
// Ensure this is called AFTER any potential $_POST processing to get updated values
$payerInfo = getPayerInfo();

// You might also need to fetch customer/product lists here if your page displays them
// (e.g., for dropdowns in a manual order creation section, if you have one)
?>