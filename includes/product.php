<section class="bg0 p-t-23 p-b-140">
    <div class="container">
        <div class="p-b-10">
            <h3 class="ltext-103 cl5">Product Overview</h3>
        </div>

        <div class="flex-w flex-sb-m p-b-52">
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
                    All Products
                </button>
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter=".women">
                    Women
                </button>
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter=".men">
                    Men
                </button>
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter=".bag">
                    Bag
                </button>
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter=".shoes">
                    Shoes
                </button>
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter=".watches">
                    Watches
                </button>
            </div>

            <div class="flex-w flex-c-m m-tb-10">
               

                <div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
                    <i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                    <i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                    Search
                </div>
            </div>

            <!-- Search product -->
            <div class="dis-none panel-search w-full p-t-10 p-b-15">
                <div class="bor8 dis-flex p-l-15">
                    <button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04">
                        <i class="zmdi zmdi-search"></i>
                    </button>
                    <input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="search-product" placeholder="Search" />
                </div>
            </div>

            <!-- Filter -->
            
        </div>

        <?php
        include 'db/DBconnnect.php';

        // Fetch all products from the database
        $sql = "SELECT p.*, c.title AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="row isotope-grid" >
            <?php foreach ($products as $product) { ?>
                <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item women" >
                    <!-- Block2 -->
                    <div class="block2">
                        <div class="block2-pic hov-img0">
                            <img  src="<?php echo htmlspecialchars('uploads/' . basename($product['image'] ?? '')); ?>" alt="IMG-PRODUCT" />
                            <a onclick="add-to-cart" href="javascript:void(0)" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo number_format($product['price'], 2); ?>" data-image="<?php echo htmlspecialchars('uploads/' . basename($product['image'] ?? '')); ?>" data-quantity="1">
                                ADD TO CART
                            </a>
                        </div>
                        <div class="block2-txt flex-w flex-t p-t-14">
                            <div class="block2-txt-child1 flex-col-l">
                                <a class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                    <?php echo htmlspecialchars($product['name'] ?? ''); ?>
                                </a>
                                <span class="stext-105 cl3 text-info"> $<?php echo number_format($product['price'] ?? 0, 2); ?> </span>
                            </div>
                            <div class="mt-2">
                                <a class="text-danger" href="<?php echo "product-detail.php?id=". $product['id']; ?>">View Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Load more -->
        <div class="flex-c-m flex-w w-full p-t-45">
            <a href="#" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
                Load More
            </a>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('.add-to-cart').forEach(function(button) {
        button.addEventListener('click', function() {
            // Get product details from the button's data attributes
            var productId = this.getAttribute('data-id');
            var productName = this.getAttribute('data-name');
            var productPrice = parseFloat(this.getAttribute('data-price'));
            var productImage = this.getAttribute('data-image');
            var quantity = parseInt(this.getAttribute('data-quantity'));

            // Create a cart item object
            var cartItem = {
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: quantity
            };

            // Send the cart item to the backend using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_to_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Update the cart UI with the response data
                    updateCart(JSON.parse(xhr.responseText));
                }
            };
            xhr.send(JSON.stringify(cartItem));
        });
    });

    // Function to update the cart UI
    function updateCart(cartItems) {
        var cartTotal = 0;
        var cartHTML = '';

        // Generate HTML for each cart item
        cartItems.forEach(function(item) {
            cartTotal += item.price * item.quantity;
            cartHTML += `
                <li class="header-cart-item flex-w flex-t m-b-12">
                    <div class="header-cart-item-img">
                        <img src="${item.image}" alt="IMG" />
                    </div>
                    <div class="header-cart-item-txt p-t-8">
                        <a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">${item.name}</a>
                        <span class="header-cart-item-info">${item.quantity} x $${item.price.toFixed(2)}</span>
                    </div>
                </li>
            `;
        });

        // Update the cart items and total
        document.querySelector('.header-cart-wrapitem').innerHTML = cartHTML;
        document.querySelector('.header-cart-total').textContent = `Total: $${cartTotal.toFixed(2)}`;

        // Add event listeners to the remove buttons
        document.querySelectorAll('.remove-cart-item').forEach(function(button) {
            button.addEventListener('click', function() {
                var productId = this.getAttribute('data-id');
                removeFromCart(productId);
            });
        });
    }

    // Function to remove an item from the cart
    function removeFromCart(productId) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'remove_from_cart.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Update the cart UI with the response data
                updateCart(JSON.parse(xhr.responseText));
            }
        };
        xhr.send(JSON.stringify({
            id: productId
        }));
    }
</script>