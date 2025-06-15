<!doctype html>
<html lang="en">

<?php include 'components/head.php'; ?>
<title>Add Products</title>

<body>
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Products</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">X</span>
                    </button>
                </div>

                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            if (isset($_SESSION['message'])) {
                            ?>
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong>Hey!</strong> <?= $_SESSION['message'] ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php
                                unset($_SESSION['message']);
                            }
                            ?>
                            <div class="card mb-0">
                                <div class="container-fluid">
                                    <!-- Add Product Form -->
                                    <form action="function.php" method="post" enctype="multipart/form-data">
                                        <!-- Product Name -->
                                        <div class="mb-3">
                                            <label for="proName" class="form-label">Product Name</label>
                                            <input type="text" class="form-control" id="proName" name="proName" required>
                                        </div>

                                        <!-- Product Description -->
                                        <div class="mb-3">
                                            <label for="proDescription" class="form-label">Description</label>
                                            <textarea class="form-control" id="proDescription" name="proDescription" rows="3"></textarea>
                                        </div>

                                        <!-- Product stock_quantity -->
                                        <div class="mb-3">
                                            <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                            <input type="number" step="1" class="form-control" id="stock_quantity" name="stock_quantity" required>
                                        </div>

                                        <!-- Product Price -->
                                        <div class="mb-3">
                                            <label for="proPrice" class="form-label">Price</label>
                                            <input type="number" step="1" class="form-control" id="proPrice" name="proPrice" required>
                                        </div>

                                        <!-- Product Category -->
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-control" id="category_id" name="category_id" required>
                                                <option value="">--- Select Category ---</option>
                                                <?php
                                                // Fetch categories from the database
                                                $sql = "SELECT * FROM categories";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->execute();
                                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($categories as $row) {
                                                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['title']) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Product Image -->
                                        <div class="mb-3">
                                            <label for="proImage" class="form-label">Product Image</label>
                                            <input type="file" class="form-control" id="proImage" name="proImage" accept="image/*" required>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" name="add-product" class="btn btn-primary">Add Product</button>
                                        <a aria-hidden="true" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</a>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>