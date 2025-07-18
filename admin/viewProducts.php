<?php
include './configs/DBconnect.php'; // Include database connection
include 'pages/products/addProducts.php';


?>

<!doctype html>
<html lang="en">
<?php include 'components/head.php'; ?>
<title>View Products</title>

<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="./index.php" class="text-nowrap logo-img">
                        <img src="./assets/images/logos/dark-logo.svg" width="180" alt="" />
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <?php include 'components/sidebarNavigation.php'; ?>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- Sidebar End -->

        <!-- Main wrapper -->
        <div class="body-wrapper">
            <!-- Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                                <i class="ti ti-bell-ringing"></i>
                                <div class="notification bg-primary rounded-circle"></div>
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <!-- <a href="https://adminmart.com/product/modernize-free-bootstrap-admin-dashboard/" target="_blank" class="btn btn-primary">Download Free</a> -->
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-mail fs-6"></i>
                                            <p class="mb-0 fs-3">My Account</p>
                                        </a>
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-list-check fs-6"></i>
                                            <p class="mb-0 fs-3">My Task</p>
                                        </a>
                                        <a href="./login.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Header End -->

            <div class="container-fluid">
                <div class="d-flex justify-content-between ">
                    <h5 class="card-title fw-semibold mb-4 ">View Products</h5>
                    <button class="btn btn-danger text-white" data-toggle="modal" data-target="#addModal">Add Product</button>
                </div>
                <div class="card mb-0 p-4 mt-2">
                    <!-- Products Table -->
                    <table class="table table-bordered">
                        <thead class="bg-info">
                            <tr class="text-white">
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include './function.php';

                            // Fetch all products from the database
                            $sql = "SELECT p.*, c.title AS category_name 
                                        FROM products p 
                                        LEFT JOIN categories c ON p.category_id = c.id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <?php
                            if (count($products) > 0) {
                                foreach ($products as $product) {
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($product['description'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($product['price'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($product['stock_quantity'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($product['image'] ?? ''); ?>" alt="Product Image" width="50">
                                        </td>
                                        <td>
                                            <!-- <a href="./pages/editProduct.php?id=<?php echo htmlspecialchars($product['id'] ?? ''); ?>" class="btn  btn-primary">Edit</a>
                                                        <a href="./pages/deleteProducts.php?id=<?php echo htmlspecialchars($product['id'] ?? ''); ?>" class="btn  btn-danger" onclick="return confirm('Are you sure you want to delete this Product?')" >Delete</a> -->
                                            <div class="dropdown show">
                                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button"
                                                    id="dropdownMenuLink" data-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">Options
                                                </a>

                                                <!-- Actions Options  -->
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <button class="dropdown-item text-info" data-toggle="modal" data-target="#editModal<?php echo htmlspecialchars($product['id'] ?? ''); ?>"
                                                        data-product-id="">Edite
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                        </svg>
                                                    </button>
                                                    <a class="dropdown-item text-danger"
                                                        href="./pages/deleteProduct.php?id=<?php echo htmlspecialchars($product['id'] ?? ''); ?>"
                                                        onclick="return confirm('Are you sure you want to delete this Product?')">Delete
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                                                        </svg>
                                                    </a>
                                                </div>

                                            </div>

                                        </td>

                                    </tr>
                                    <?php include 'pages/Products/editProduct.php'; ?>


                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center">No products found.</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                </div>


            </div>
        </div>
    </div>

    <?php include 'components/js.php' ?>
</body>

</html>