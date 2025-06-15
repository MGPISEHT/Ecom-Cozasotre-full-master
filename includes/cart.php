
<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>
    <div class="header-cart flex-col-l p-l-65 p-r-25">
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl2">កន្ត្រកទំនិញរបស់អ្នក</span>
            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <div class="header-cart-content flex-w js-pscroll">
            <ul class="header-cart-wrapitem w-full">
            </ul>

            <div class="w-full">
                <div class="header-cart-total w-full p-tb-40">
                    សរុប: $0.00
                </div>

                <div class="header-cart-buttons flex-w w-full">
                    <a href="shoping-cart.php" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                        មើលកន្ត្រក
                    </a>
                    <a href="shoping-cart.php" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                        ទូទាត់
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function handleCartActions() {
        // Load cart data when the page loads
        function loadCart() {
            fetch('get_cart.php')
                .then(response => response.json())
                .then(cartData => {
                    updateCart(cartData);
                })
                .catch(error => console.error('Error loading cart:', error));
        }

        // Function to update the cart UI
        function updateCart(cartItems) {
            var cartList = document.querySelector('.header-cart-wrapitem');
            var cartTotal = document.querySelector('.header-cart-total');
            var cartIcon = document.querySelector('#cart');

            cartList.innerHTML = ''; // Clear cart before updating
            var totalAmount = 0;
            var itemCount = 0;

            cartItems.forEach(function(item) {
                totalAmount += item.price * item.quantity;
                itemCount += item.quantity;

                const listItem = document.createElement('li');
                listItem.classList.add('header-cart-item', 'flex-w', 'flex-t', 'm-b-12');
                listItem.innerHTML = `
                <div class="header-cart-item-img">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="header-cart-item-txt p-t-8">
                    <a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">${item.name}</a>
                    <span class="header-cart-item-info">${item.quantity} x $${item.price.toFixed(2)}</span>
                    <a href="#" class="js-delete-item bg-danger" data-id="${item.id}">Delete</a>
                    
                </div>
            `;
                cartList.appendChild(listItem);
            });

            cartTotal.textContent = `សរុប: $${totalAmount.toFixed(2)}`;

            if (cartIcon) {
                cartIcon.setAttribute('data-notify', itemCount); // Update cart count on icon
            }

            // Add event listeners for delete buttons (using event delegation)
            cartList.addEventListener('click', function(event) {
                if (event.target.classList.contains('js-delete-item')) {
                    event.preventDefault(); // Prevent default action
                    const itemId = event.target.getAttribute('data-id');
                    deleteCartItem(itemId);
                }
            });
        }

        // Function to delete a cart item
        function deleteCartItem(itemId) {
            fetch('delete_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: itemId
                    })
                })
                .then(response => response.json())
                .then(cartData => {
                    updateCart(cartData); // Update the cart UI after deletion
                })
                .catch(error => console.error('Error deleting item:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadCart(); // Load cart data when the page loads

            document.querySelectorAll('.js-add-to-cart').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default action

                    var cartItem = {
                        id: this.getAttribute('data-id'),
                        name: this.getAttribute('data-name'),
                        price: parseFloat(this.getAttribute('data-price')),
                        image: this.getAttribute('data-image'),
                        quantity: parseInt(this.getAttribute('data-quantity'))
                    };

                    // Send data to PHP via AJAX
                    fetch('add_to_cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(cartItem)
                        })
                        .then(response => response.json())
                        .then(cartData => {
                            updateCart(cartData);
                            document.querySelector('.wrap-header-cart').classList.add('show-cart'); // Show cart
                        })
                        .catch(error => console.error('Error adding to cart:', error));
                });
            });
        });
    }

    handleCartActions();
</script>