<?php
// --- 1. Session and Database Initialization ---
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    if (!isset($_SESSION["valid"])) {
        header("Location: login.php");
        exit;
    }
}

include 'db/DBconnnect.php';

// --- 2. Helper Functions for Cart Management ---
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
        $quantity = filter_var($item['quantity'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        if ($price !== false && $quantity !== false) {
            $total += ($price * $quantity);
        }
    }
    return $total;
}

// --- 3. Handle AJAX Cart Actions (Add, Remove, Update Quantity) ---
if (isset($_POST['action'])) {
    $response = ['success' => false, 'message' => 'An unknown error occurred.'];
    header('Content-Type: application/json');
    // if ($_POST['action'] === 'addToCart')​ សម្រង់ការបន្ថែមទំនិញទៅក្នុងកន្ត្រក
    if ($_POST['action'] === 'addToCart') {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_URL);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);

        if ($id && $name && $price !== false && $image && $quantity !== false) {
            $itemToAdd = [
                'id' => (int) $id,
                'name' => $name,
                'price' => (float) $price,
                'image' => $image,
                'quantity' => (int) $quantity
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
            $response = ['success' => true, 'message' => 'Item added to cart.'];
        } else {
            $response['message'] = 'Invalid item data provided for adding to cart.';
        }
    } elseif ($_POST['action'] === 'removeItem') {
        $itemId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($itemId) {
            $cart = getCart();
            $itemRemoved = false;
            foreach ($cart as $key => $item) {
                if ($item['id'] == $itemId) {
                    unset($cart[$key]);
                    $itemRemoved = true;
                    break;
                }
            }
            updateCart(array_values($cart)); // Re-index array
            if ($itemRemoved) {
                $response = ['success' => true, 'message' => 'Item removed successfully.'];
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
            $updated = false;
            foreach ($cartData as $itemData) {
                $itemId = filter_var($itemData['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
                $newQuantity = filter_var($itemData['quantity'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

                // តម្លៃត្រូវបានធានាថាមិនតូចជាង ០ ឬ អវិជ្ជមាន
                if ($itemId && $newQuantity !== false && $newQuantity >= 0) {
                    foreach ($cart as &$item) {
                        if ($item['id'] == $itemId) {
                            $item['quantity'] = (int) $newQuantity;
                            $updated = true;
                            break;
                        }
                    }
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
                'success' => $updated,
                'total' => number_format($total, 2),
                'item_totals' => $itemTotals,
                'message' => $updated ? 'Cart quantities updated.' : 'No quantities were updated.'
            ];
        } else {
            $response['message'] = 'Invalid data for quantity update.';
        }
    }
    echo json_encode($response);
    exit();
}

// --- 4. Handle Traditional Form Submission (Order Placement) ---
if (isset($_POST['place_order'])) {
    // ប្រើ filter_input() ដើម្បីសម្អាតទិន្នន័យពី POST
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '';
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';

    // ធានាថា customer ត្រូវបាន Login ហើយ
    $customer_id = $_SESSION['customer_id'] ?? null;
    if (!$customer_id) {
        header('location: login.php?message=Please log in to complete your order.');
        exit;
    }
    $cart = getCart();

    if (empty($cart)) {
        header('location: shoping-cart.php?message=Your cart is empty. Please add items before placing an order.');
        exit;
    }
    // Crucial: ប្រើសម្រាប់គណនាតម្លៃសរុបនៃការកម្មង់
    $order_cost = calculateCartTotal($cart);
    $order_status = "on_hold";
    $order_date = date('Y-m-d H:i:s');
    try {
        $conn->beginTransaction();
        $stmt_orders = $conn->prepare("INSERT INTO orders (order_cost, status, customer_id, customer_city, customer_phone, customer_address, order_date)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_orders->execute([$order_cost, $order_status, $customer_id, $city, $phone, $address, $order_date]);
        $order_id = $conn->lastInsertId();
        foreach ($cart as $product) {
            $product_id = filter_var($product['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
            $product_name = filter_var($product['name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_image = filter_var($product['image'] ?? '', FILTER_SANITIZE_URL);
            $product_price = filter_var($product['price'] ?? 0, FILTER_VALIDATE_FLOAT);
            $product_quantity = filter_var($product['quantity'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

            if ($product_id > 0 && $product_name && $product_price !== false && $product_quantity > 0) {
                $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, , price, quantity)
                                                VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_items->execute([$order_id, $product_id, $product_name, $price, $quantity]);
            } else {
                error_log("Invalid product data in cart for order ID: " . $order_id . " Product: " . print_r($product, true));
            }
        }

        $conn->commit();
        unset($_SESSION['cart']);
        header('location: order_success.php?order_id=' . $order_id);
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("DB Error placing order: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        header('location: order_failed.php?message=A database error occurred. Please try again.');
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("General Error placing order: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        header('location: order_failed.php?message=An unexpected error occurred. Please try again.');
        exit;
    }
}

if (isset($_POST['username']) && !isset($_POST['action'])) {
    $_SESSION['payer_info'] = [
        'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '',
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '',
        'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '',
        'country' => filter_input(INPUT_POST, 'country', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '',
        'address' => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '',
    ];
}
function getPayerInfo(): array
{
    return $_SESSION['payer_info'] ?? [];
}
$cart = getCart();
$total = calculateCartTotal($cart);
$_SESSION['total_cart_cost'] = $total;

?>

<div class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
                <div class="m-l-25 m-r--38 m-lr-0-xl">
                    <div class="wrap-table-shopping-cart">
                        <table class="table-shopping-cart">
                            <tr class="table_head">
                                <th class="column-1">Product</th>
                                <th class="column-2">Name</th>
                                <th class="column-3">Price</th>
                                <th class="column-4">Quantity</th>
                                <th class="column-5">Total</th>
                                <th class="column-6">Action</th>
                            </tr>

                            <?php
                            if (!empty($cart)) {
                                foreach ($cart as $item) {
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    echo '
                                    <tr class="table_row">
                                        <td class="column-1">
                                            <div class="how-itemcart1">
                                                <img src="' . htmlspecialchars($item['image']) . '" alt="IMG">
                                            </div>
                                        </td>
                                        <td class="column-2">' . htmlspecialchars($item['name']) . '</td>
                                        <td class="column-3 price-per-item" data-price="' . htmlspecialchars($item['price']) . '">$' . number_format($item['price'], 2) . '</td>
                                        <td class="column-4">
                                            <div class="wrap-num-product flex-w m-l-auto m-r-0">
                                                <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                    <i class="fs-16 zmdi zmdi-minus"></i>
                                                </div>
                                                <input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product' . htmlspecialchars($item['id']) . '" value="' . htmlspecialchars($item['quantity']) . '" data-id="' . htmlspecialchars($item['id']) . '" min="1">
                                                <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                    <i class="fs-16 zmdi zmdi-plus"></i>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="column-5 product-total">$' . number_format($itemTotal, 2) . '</td>
                                        <td class="column-6">
                                            <button class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10 remove-item" data-id="' . htmlspecialchars($item['id']) . '">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">Your cart is empty. Add some items to checkout!</td></tr>';
                            }
                            ?>
                        </table>
                    </div>

                    <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                        <div class="p-t-15">
                            <div class="flex-w">
                                <button
                                    class="flex-c-m stext-101 cl2 size-115 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer update-cart-btn"
                                    type="button">
                                    Update Amount
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">
                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
                    <h4 class="mtext-109 cl2 p-b-30">
                        Cart Totals
                    </h4>

                    <div class="flex-w flex-t bor12 p-b-13">
                        <div class="size-208">
                            <span class="stext-110 cl2">
                                Subtotal:
                            </span>
                        </div>
                        <div class="size-209 p-t-1">
                            <span class="mtext-110 cl2 cart-subtotal">
                                $<?php echo number_format($total, 2); ?>
                            </span>
                        </div>
                    </div>

                    <div class="container mt-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Shipping Information</h5>
                            </div>
                            <div class="card-body">
                                <form id="checkout-form">
                                    <input type="hidden" id="customer_id" name="customer_id"
                                        value="<?= htmlspecialchars($payerInfo['customer_id'] ?? '') ?>">

                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="recipient_name"
                                            name="recipient_name"
                                            value="<?= htmlspecialchars($payerInfo['username'] ?? '') ?>"
                                            placeholder="Username" required>
                                    </div>

                                    <div class="mb-3">
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?= htmlspecialchars($payerInfo['email'] ?? '') ?>"
                                            placeholder="Email" required>
                                    </div>

                                    <div class="mb-3">
                                        <input type="tel" class="form-control" id="shipping_phone" name="shipping_phone"
                                            value="<?= htmlspecialchars($payerInfo['phone'] ?? '') ?>"
                                            placeholder="Phone" required>
                                    </div>

                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="country" name="country"
                                            value="<?= htmlspecialchars($payerInfo['country'] ?? '') ?>"
                                            placeholder="Country" required>
                                    </div>

                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="address_line1" name="address_line1"
                                            value="<?= htmlspecialchars($payerInfo['address'] ?? '') ?>"
                                            placeholder="Address" required>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Total:</strong> $<span
                                            id="total_amount_display"><?= number_format($total, 2) ?></span>
                                    </div>
                                </form>

                                <div class="mt-4" id="paypal-button-container"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://www.paypal.com/sdk/js?client-id=TNC34ZAEBJK4Y&currency=USD"></script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script
    src="https://www.paypal.com/sdk/js?client-id=AfkfEA59DV9179OsB5kuRYFTzdu4Ap7KZTKe9peAbqHH7Q0L5JFJeQcDFKP9qEpvObffMNaTVILlUfqC&currency=USD"></script>
<script>
    $(document).ready(function () {
        // Function to update the cart total and individual product totals on the UI
        function updateCartTotalsUI() {
            let grandTotal = 0;
            $(".table_row").each(function () {
                let quantity = parseInt($(this).find(".num-product").val());
                let price = parseFloat($(this).find(".price-per-item").data("price"));
                let itemTotal = quantity * price;
                $(this).find(".product-total").text("$" + itemTotal.toFixed(2));
                grandTotal += itemTotal;
            });
            $(".cart-total").text("$" + grandTotal.toFixed(2));
            $(".cart-subtotal").text("$" + grandTotal.toFixed(2));
            $("#total_amount_input").val(grandTotal.toFixed(2)); // Update hidden input for checkout
        }

        // Event listener for quantity input changes
        $(".table-shopping-cart").on("input", ".num-product", function () {
            // Ensure quantity is at least 1
            if (parseInt($(this).val()) < 1 || isNaN(parseInt($(this).val()))) {
                $(this).val(1);
            }
            updateCartTotalsUI(); // Update UI immediately on input change
        });

        // Event listener for quantity increase/decrease buttons
        $(".table-shopping-cart").on("click", ".btn-num-product-down", function () {
            let $input = $(this).next(".num-product");
            let currentVal = parseInt($input.val());
            if (currentVal > 1) {
                $input.val(currentVal - 1).trigger('input'); // Trigger input event to update totals
            }
        });

        $(".table-shopping-cart").on("click", ".btn-num-product-up", function () {
            let $input = $(this).prev(".num-product");
            let currentVal = parseInt($input.val());
            $input.val(currentVal + 1).trigger('input'); // Trigger input event to update totals
        });

        // Event listener for the "Update Cart" button (sends data to PHP)
        $(".update-cart-btn").on("click", function () {
            let cartData = [];
            $(".table_row").each(function () {
                let id = $(this).data("id");
                let quantity = $(this).find(".num-product").val();
                cartData.push({
                    id: id,
                    quantity: quantity
                });
            });

            $.ajax({
                url: "", // Send to the same file (this script)
                type: "POST",
                data: {
                    action: "updateQuantities",
                    cart: cartData
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        // Update UI with new totals from server response
                        $(".cart-total").text("$" + response.total);
                        $(".cart-subtotal").text("$" + response.total);
                        $("#total_amount_input").val(response.total);

                        // Update individual item totals
                        $(".table_row").each(function (index) {
                            $(this).find(".product-total").text("$" + response.item_totals[index]);
                        });
                        console.log("Cart updated successfully: " + response.message);
                    } else {
                        console.error("Failed to update cart: " + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error updating quantities:", error);
                    // Optionally, show a user-friendly error message
                }
            });
        });

        // Event listener for "Remove" item buttons
        $(".remove-item").on("click", function (e) {
            e.preventDefault(); // Prevent default button behavior
            const $button = $(this); // Cache 'this' reference
            const itemId = $button.data("id");

            if (!confirm("Are you sure you want to remove this item?")) {
                return; // Stop if user cancels
            }

            $.ajax({
                url: "shoping-cart.php",

                type: "POST",
                data: {
                    action: "removeItem",
                    id: itemId
                },
                dataType: "json",
                beforeSend: function () {
                    // Optional: Show loading state
                    $button.prop('disabled', true).text('...');
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert("Error removing item from cart: " + (response.message || "Unknown reason."));
                        console.error("Server reported error:", response.message);
                        $button.prop('disabled', false).text('Remove');
                    }
                },

                complete: function () {
                    // This runs whether success or error
                    // $button.prop('disabled', false).text('Remove'); // Moved to success/error for specific re-enabling
                }
            });
        });

        // PayPal Integration
        paypal.Buttons({
            createOrder: function (data, actions) {
                // បង្កើត order នៅ PayPal តាមតម្លៃសរុប
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= number_format($total, 2, '.', '') ?>'
                        }
                    }]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    // បន្ទាប់ពីទូទាត់ជោគជ័យ, ផ្ញើទិន្នន័យទៅ backend
                    fetch('paypal_capture.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            orderID: data.orderID,
                            payerID: data.payerID,
                            paymentID: details.id,
                            customer_id: document.getElementById('customer_id').value,
                            recipient_name: document.getElementById('recipient_name').value,
                            email: document.getElementById('email').value,
                            shipping_phone: document.getElementById('shipping_phone').value,
                            country: document.getElementById('country').value,
                            address_line1: document.getElementById('address_line1').value,
                            total_amount: '<?= number_format($total, 2, '.', '') ?>',
                            payment_method: 'PayPal'
                        })
                    }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Payment Success! Order ID: ' + data.order_id);
                                window.location.href = "success.php?order_id=" + data.order_id;
                            } else {
                                alert('Payment processing failed: ' + data.message);
                            }
                        });
                });
            }
        }).render('#paypal-button-container');
    });
</script>