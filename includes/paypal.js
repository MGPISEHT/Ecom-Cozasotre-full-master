
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
                let totalAmount = $(".cart-total").text().replace("$", "");
                if (parseFloat(totalAmount) <= 0) {
                    console.error("Cannot create order with zero or negative total amount.");
                    return false;
                }
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: totalAmount
                        }
                    }]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    console.log("Transaction completed by " + details.payer.name.given_name);
                    window.location.href = "success.php";
                });
            },
            onCancel: function (data) {
                console.log('Order cancelled by the user.');
            },
            onError: function (err) {
                console.error('An error occurred during PayPal transaction:', err);
            }
        }).render("#paypal-button-container");
        updateCartTotalsUI();
    });
