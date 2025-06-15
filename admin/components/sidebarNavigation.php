<nav class="sidebar-nav scroll-sidebar" data-simplebar="">
    <ul id="sidebarnav">
        
        <li class="sidebar-item">
            <a class="sidebar-link" href="./index.php" aria-expanded="false">
                <span class="hide-menu">Dashboard</span>
                <span>
                    <i class="ti ti-layout-dashboard"></i>
                </span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a href="viewCategories.php" class="sidebar-link d-flex align-items-center toggle-submenu" href="javascript:void(0);">
                <span class="hide-menu">Management Categories</span>
                <span class="me-2">
                    <i class="fa-solid fa-list"></i>
                </span>
                <!-- <i class="fa fa-chevron-down ms-auto"></i> -->
            </a>

        </li>


        <li class="sidebar-item">
            <a href="viewProducts.php" class="sidebar-link d-flex align-items-center toggle-submenu" href="javascript:void(0);">

                <span class="hide-menu">Management Products</span>
                <span class="me-2">
                    <i class="fa-brands fa-product-hunt"></i>
                </span>
                <!-- <i class="fa fa-chevron-down ms-auto"></i> -->
            </a>

        </li>

        <li class="sidebar-item">
            <a href="viewPayment.php" class="sidebar-link d-flex align-items-center toggle-submenu" href="javascript:void(0);">
                <span class="hide-menu">Management Payments</span>
                <span class="me-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </span>
                <!-- <i class="fa fa-chevron-down ms-auto"></i> -->
            </a>
        </li>

        <li class="sidebar-item">
            <a href="viewOrder.php" class="sidebar-link d-flex align-items-center toggle-submenu" href="javascript:void(0);">
                <span class="hide-menu">Management Orders</span>
                <span class="me-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                </span>
                <!-- <i class="fa fa-chevron-down ms-auto"></i> -->
            </a>
            
        </li>
        <li class="sidebar-item">
            <a href="viewCustomer.php" class="sidebar-link d-flex align-items-center toggle-submenu" href="javascript:void(0);">
                <span class="hide-menu">Management Customer</span>
                <span class="me-2">
                    <i class="fa-solid fa-users"></i>
                </span>
                <!-- <i class="fa fa-chevron-down ms-auto"></i> -->
            </a>
            
        </li>
        <li class="sidebar-item">
            <a href="viewUsers.php" class="sidebar-link d-flex align-items-center toggle-submenu" href="javascript:void(0);">
                <span class="hide-menu">Management Users</span>
                <span class="me-2">
                    <i class="fa-solid fa-circle-user"></i>
                </span>
                <!-- <i class="fa fa-chevron-down ms-auto"></i> -->
            </a>

        </li>


        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">AUTH</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="./login.php" aria-expanded="false">
                <span>
                    <i class="ti ti-login"></i>
                </span>
                <span class="hide-menu">Login</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="./register.php" aria-expanded="false">
                <span>
                    <i class="ti ti-user-plus"></i>
                </span>
                <span class="hide-menu">Register</span>
            </a>
        </li>

    </ul>

</nav>


// Import the necessary CSS and JavaScript files:
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarLinks = document.querySelectorAll(".toggle-submenu");

        sidebarLinks.forEach(link => {
            link.addEventListener("click", function() {
                const parentLi = this.parentElement;

                // Close other open submenus
                document.querySelectorAll(".sidebar-item").forEach(item => {
                    if (item !== parentLi) {
                        item.classList.remove("active");
                    }
                });

                // Toggle active class on click
                parentLi.classList.toggle("active");
            });
        });
    });
</script>